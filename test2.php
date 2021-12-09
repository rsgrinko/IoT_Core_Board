<?php
/**
 * Файл для тестирования и отладки нового функционала
 */
require_once __DIR__.'/inc/bootstrap.php';
if(!CUser::is_admin()) {
    die('403 - Access denied');
}

$output=null;
$retval=null;
exec('uptime', $output, $retval);


    $mail = new CMail;
    $mail->dump = true;
    $mail->dumpPath = $_SERVER['DOCUMENT_ROOT'].'/uploads/emails';
    $mail->from('iot@'.$_SERVER['SERVER_NAME'], 'Система оповещений IoT Core');
    $mail->to(ADMIN_EMAIL, 'Администратор панели');
    $mail->subject = 'Проверка почтовой подсистемы';
    $mail->assignTemplateVars(
        array(
            'HEADER' => 'IoT Core Board',
            'MESSAGE' => '<pre>'.print_r($output, true).'</pre>',
            'TITLE' => 'Код: '.$retval,
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