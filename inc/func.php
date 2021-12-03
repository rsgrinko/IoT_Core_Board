<?php
/**
*	Вспомогательные функции
*	Данный файл входит в состав системы IoT Core System
*	Разработчик: Роман Сергеевич Гринько
*	E-mail: rsgrinko@gmail.com
*   Сайт: https://it-stories.ru
*/	


/**
 * Получение имени CSS класса по типу события
 * @param string $type
 */
function getClassNameByEventType($type){
	$result = '';
	switch($type){
		case 'info':
			$result = 'btn btn-app-blue btn-block';
		break;	
			
		case 'warning':
			$result = 'btn btn-app-red btn-block';
		break;	
		
		case 'success':
			$result = 'btn btn-app-green btn-block';
		break;
		
		case 'notice':
			$result = 'btn btn-app-orange btn-block';
		break;
					
		default:
			$result = 'btn btn-app-blue btn-block';
		break;
	}
	
	return $result;
}
	
/**
 * Обработка строки перед подстановкой в SQL зарпос
 * @param string $str
 */	
function prepareString($str){
	$str = str_replace('\'', '', $str);
	return $str;
}


/**
 * Вывод массива в понятном виде на страницу
 * @param array $arr
 * @param bool $stop
 */
function pre($arr, $stop = false) {
	echo '<pre>'.print_r($arr, true).'</pre>';
	
	if($stop) {
		die();
	}
}

/**
 * Проверка принадлежности пользователя к устройтсву
 * 
 * @param int $deviceId
 * @param int $userId
 */
function isHaveAccessToDevice($deviceId, $userId){
	global $DB;
	$arDevice = $DB->query('SELECT user FROM devices WHERE id="'.$deviceId.'"');
	
	if($arDevice){
		if($arDevice[0]['user'] == $userId){
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

/**
 * Получение ip пользователя
 */
function getIp() {
		  $keys = [
		    'HTTP_CLIENT_IP',
		    'HTTP_X_FORWARDED_FOR',
		    'REMOTE_ADDR'
		  ];
		  foreach ($keys as $key) {
		    if (!empty($_SERVER[$key])) {
		      $ip = trim(end(explode(',', $_SERVER[$key])));
		      if (filter_var($ip, FILTER_VALIDATE_IP)) {
		        return $ip;
		      }
		    }
		  }
	}

/**
 * Получение информации о клиенте
 */
function getClientInfo() {
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if (preg_match('/opera/', $userAgent)) {
            $name = 'opera';
        }
        elseif (preg_match('/yabrowser/', $userAgent)) {
            $name = 'yabrowser';
        }
        elseif (preg_match('/chrome/', $userAgent)) {
            $name = 'chrome';
        }
        elseif (preg_match('/webkit/', $userAgent)) {
            $name = 'safari';
        }
        elseif (preg_match('/msie/', $userAgent)) {
            $name = 'msie';
        }
        elseif (preg_match('/mozilla/', $userAgent) && !preg_match('/compatible/', $userAgent)) {
            $name = 'mozilla';
        }
        else {
            $name = 'unrecognized';
        }

        if (preg_match('/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/', $userAgent, $matches)) {
            $version = $matches[1];
        }
        else {
            $version = 'unknown';
        }

        if (preg_match('/linux/', $userAgent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/', $userAgent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/', $userAgent)) {
            $platform = 'windows';
        }
        else {
            $platform = 'unrecognized';
        }
        
        $keys = [
		    'HTTP_CLIENT_IP',
		    'HTTP_X_FORWARDED_FOR',
		    'REMOTE_ADDR'
		  ];
		$real_ip = '';
		  foreach ($keys as $key) {
		    if (!empty($_SERVER[$key])) {
		      $ip = trim(end(explode(',', $_SERVER[$key])));
		      if (filter_var($ip, FILTER_VALIDATE_IP)) {
		        $real_ip = $ip;
		        break;
		      }
		    }
		  }

        return array(
            'name'      => $name,
            'version'   => $version,
            'platform'  => $platform,
            'userAgent' => $userAgent,
            'ip' 		=> $real_ip
        );
}

/**
 * Получение устройств, принадлежащих пользователю
 * 
 * @param int $userId
 * @return array|bool
 */
function getUserDevices($userId) {
	global $DB;
	$res = $DB->query('SELECT * FROM devices WHERE user="'.$userId.'"');
	if($res){
		return $res;
	} else {
		return false;
	}
}

/**
 * Получение ОС пользователя
 * 
 * @return string
 */
function getOS() {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $oses = [
                'iPhone'         => '/(iPhone)|(iPad)/i',
                'Windows 3.11'   => '/Win16/i',
                'Windows 95'     => '/(Windows 95)|(Win95)|(Windows_95)/i',
                'Windows 98'     => '/(Windows 98)|(Win98)/i',
                'Windows 2000'   => '/(Windows NT 5.0)|(Windows 2000)/i',
                'Windows XP'     => '/(Windows NT 5.1)|(Windows XP)/i',
                'Windows 2003'   => '/(Windows NT 5.2)/i',
                'Windows Vista'  => '/(Windows NT 6.0)|(Windows Vista)/i',
                'Windows 7'      => '/(Windows NT 6.1)|(Windows 7)/i',
                'Windows 8'      => '/(Windows NT 6.2)|(Windows 8)/i',
                'Windows 8.1'    => '/(Windows NT 6.3)|(Windows 8.1)/i',
                'Windows 10'     => '/(Windows NT 10.0)|(Windows 10)/i',
                'Windows NT 4.0' => '/(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)/i',
                'Windows ME'     => '/Windows ME/i',
                'Open BSD'       => '/OpenBSD/i',
                'Sun OS'         => '/SunOS/i',
                'Android'        => '/Android/i',
                'Linux'          => '/(Linux)|(X11)/i',
                'Macintosh'      => '/(Mac_PowerPC)|(Macintosh)/i',
                'QNX'            => '/QNX/i',
                'BeOS'           => '/BeOS/i',
                'OS/2'           => '/OS/2/i',
                'Search Bot'     => '/(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)/i',
            ];

            foreach ($oses as $os => $pattern) {
                if (preg_match($pattern, $userAgent)) {
                    return $os;
                }
            }
            return 'Unknown OS';
        }


/**
 * Отправка уведомления на почту администратору
 */
 function adminSendMail($subject, $message, $file = false){
    $mail = new CMail;
    $mail->from('iot@'.$_SERVER['SERVER_NAME'], 'Система оповещений IoT Core');
    $mail->to(ADMIN_EMAIL, 'Администратор панели');
    $mail->subject = $subject;
    $mail->body = '
        <h1>Уведомление от системы IoT Core</h1>
        <p>'.$message.'</p>
        <hr>
        <p>
            С уважением, система IoT Core Board v.'.VERSION.'
            <br><a href="http://'.$_SERVER['SERVER_NAME'].'">http://'.$_SERVER['SERVER_NAME'].'</a>
        </p>
    ';
     
    // прикрепляем лог, если он есть
    if(isset($file) and !empty($file) and file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $file)){
        $mail->addFile($_SERVER['DOCUMENT_ROOT'] . '/' . $file);
    }
    $mail->send();

    return;
 }