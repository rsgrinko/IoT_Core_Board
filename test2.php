<?php
/**
 * Файл для тестирования и отладки нового функционала
 */
require_once __DIR__.'/inc/bootstrap.php';
if(!CUser::is_admin()) {
    die('403 - Access denied');
}


/**
 * Класс для отправки E-Mail с использованием почтовых шаблонов
 */
class CMail2 {
    /**
     * Шаблон письма
     */
    public $template = 'default';
    
    /**
     * Дирректория с шаблонами писем
     */
    public $templateDir = '/mail_templates/';

	/**
	 * От кого
	 */
	public $fromEmail = '';
	public $fromName = '';
	
	/**
	 * Кому
	 */
	public $toEmail = '';
	public $toName = '';
	
	/**
	 * Тема
	 */
	public $subject = '';

    /**
	 * Массив с данными для подстановки в шаблон
	 */
	public $arrTemplate = [];

    /**
	 * Массив заголовков файлов
	 */
	private $_files = array();

    /**
	 * Управление дампированием
	 */
	public $dump = false;

    /**
	 * Директория куда сохранять письма
	 */
	public $dumpPath = '';

    
    /**
	 * От кого
	 */
	public function from($email, $name = null)
	{
		$this->fromEmail = $email;
		$this->fromName = $name;
	}
	
	/**
	 * Кому
	 */
	public function to($email, $name = null)
	{
		$this->toEmail = $email;
		$this->toName = $name;
	}

    /**
     * Установка переменных шаблона
     */
	public function assignTemplateVars($arrVars) {
		$this->arrTemplate = $arrVars;
	}


    /**
	 * Добавление файла к письму
	 */
	public function addFile($filename)
	{
		if (is_file($filename)) {
			$name = basename($filename);
			$fp   = fopen($filename, 'rb');  
			$file = fread($fp, filesize($filename));   
			fclose($fp);
			$this->_files[] = array( 
				'Content-Type: application/octet-stream; name="' . $name . '"',   
				'Content-Transfer-Encoding: base64',  
				'Content-Disposition: attachment; filename="' . $name . '"',   
				'',
				chunk_split(base64_encode($file)),
			);
		}
	}

    /**
	 * Проверка существования файла
	 * Если директория не существует - пытается её создать.
	 * Если файл существует - к концу файла приписывает префикс.
	 */
	private function safeFile($filename)
	{
		$dir = dirname($filename);
		if (!is_dir($dir)) {
			mkdir($dir, 0777, true);
		}
		
		$info   = pathinfo($filename);
		$name   = $dir . '/' . $info['filename']; 
		$ext    = (empty($info['extension'])) ? '' : '.' . $info['extension'];
		$prefix = '';
		
		if (is_file($name . $ext)) {
			$i = 1;
			$prefix = '_' . $i;
			
			while (is_file($name . $prefix . $ext)) {
				$prefix = '_' . ++$i;
			}
		}
		
		return $name . $prefix . $ext;
	}

    /**
     * Отправка
     */
    public function send() {
		if (empty($this->toEmail)) {
			return false;
		}
		
		// От кого
		$from = (empty($this->fromName)) ? $this->fromEmail : '=?UTF-8?B?' . base64_encode($this->fromName) . '?= <' . $this->fromEmail . '>';
		
		// Кому
		$array_to = array();
		foreach (explode(',', $this->toEmail) as $row) {
			$row = trim($row);
			if (!empty($row)) {
				$array_to[] = (empty($this->toName)) ? $row : '=?UTF-8?B?' . base64_encode($this->toName) . '?= <' . $row . '>';
			}
		}
		
		// Тема письма
		$subject = (empty($this->subject)) ? 'No subject' : $this->subject;
		

		$body = file_get_contents($this->templateDir.'/'.$this->template.'.html');

		foreach($this->arrTemplate as $templateKey => $templateValue){
			$body = str_replace('{'.$templateKey.'}', $templateValue, $body);
		}

	
		$boundary = md5(uniqid(time()));
		
		// Заголовок письма
		$headers = array(
			'Content-Type: multipart/mixed; boundary="' . $boundary . '"',
			'Content-Transfer-Encoding: 7bit',
			'MIME-Version: 1.0',
			'From: ' . $from,
			'Date: ' . date('r')
		);
		
		// Тело письма
		$message = array(
			'--' . $boundary,
			'Content-Type: text/html; charset=UTF-8',
			'Content-Transfer-Encoding: base64',
			'',
			chunk_split(base64_encode($body))
		);
		
		if (!empty($this->_files)) {
			foreach ($this->_files as $row) {
				$message = array_merge($message, array('', '--' . $boundary), $row);
			}
		}
		
		$message[] = '';
		$message[] = '--' . $boundary . '--';
		$res = array();
		
		foreach ($array_to as $to) {
			// Дамп письма в файл
			if ($this->dump == true) {
				if (empty($this->dumpPath)) {
					$this->dumpPath = dirname(__FILE__) . '/uploads';
				}
				
				$dump = array_merge($headers, array('To: ' . $to, 'Subject: ' . $subject, ''), $message);
				$file = $this->safeFile($this->dumpPath . '/' . date('Y-m-d_H-i-s') . '.eml');
				file_put_contents($file, implode("\r\n", $dump));
			}
			$res[] = mb_send_mail($to, $subject, implode("\r\n", $message), implode("\r\n", $headers));
		}
		
		return $res;
	}
}






    $mail = new CMail2;
    $mail->dump = true;
    $mail->dumpPath = $_SERVER['DOCUMENT_ROOT'].'/uploads';
    $mail->from('iot@'.$_SERVER['SERVER_NAME'], 'Система оповещений IoT Core');
    $mail->to(ADMIN_EMAIL, 'Администратор панели');
    $mail->subject = 'Проверка почтовой подсистемы';
    $mail->assignTemplateVars(
        array(
            'HEADER' => 'IoT Core Board',
            'MESSAGE' => 'Произошла проверка почтовой рассылки с помощью нового класса',
            'TITLE' => 'Уведомление системы',
            'LINK' => 'https://it-stories.ru/',
            'LINKNAME' => 'Перейти в панель',
            'FOOTER' => 'Сообщение сгенерировано автоматически',
            'SERVERNAME' => 'it-stories.ru'
        )
    );

    $mail->template = 'default';
    $mail->templateDir = $_SERVER['DOCUMENT_ROOT'].'/assets/mail_templates';
    

    // прикрепляем лог, если он есть
    if(isset($file) and !empty($file) and file_exists($file)){
        $mail->addFile($file);
    }
    $mail->send();