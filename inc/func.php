<?php
/**
*	Вспомогательные функции
*	Данный файл входит в состав системы IoT Core System
*	Разработчик: Роман Сергеевич Гринько
*	E-mail: rsgrinko@gmail.com
*   Сайт: https://it-stories.ru
*/

/**
 * Проверка на создателя для критического функционала
 * TODO: переделать
 *
 * @param $id
 */
function isGod($id) {
    if($id == '1') {
        return true;
    } else {
        return false;
    }
}

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
    $cacheId = md5('isHaveAccessToDevice_'.$deviceId.'_'.$userId);
    if(CCache::check($cacheId)){
        $arDevice = CCache::get($cacheId);
    } else {
        global $DB;
        $arDevice = $DB->query('SELECT user FROM devices WHERE id="'.$deviceId.'"');
        CCache::write($cacheId, $arDevice);
    }


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
    $cacheId = md5('getUserDevices_'.$userId);
    if(CCache::check($cacheId)){
        $res = CCache::get($cacheId);
    } else {
        global $DB;
        $res = $DB->query('SELECT * FROM devices WHERE user="'.$userId.'"');
        CCache::write($cacheId, $res);
    }

	if($res){
		return $res;
	} else {
		return [];
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
function adminSendMail($subject, $message, $file = false)
{
    $mail = new CMail;
    $mail->dump = true;
    $mail->dumpPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/emails';
    $mail->from('iot@' . $_SERVER['SERVER_NAME'], 'IoT Core Board v.' . VERSION);
    $mail->to(ADMIN_EMAIL, 'Администратор панели');
    $mail->subject = $subject;
    $mail->assignTemplateVars(
        array(
            'HEADER' => 'Автоматическая рассылка',
            'MESSAGE' => $message,
            'TITLE' => 'Уведомление от панели',
            'LINK' => 'https://new-dev.it-stories.ru/',
            'LINKNAME' => 'Перейти в панель',
            'HOME' => 'https://it-stories.ru'
        )
    );

    $mail->template = 'default_red';
    $mail->templateDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/mail_templates';

    // прикрепляем лог, если он есть
    if (isset($file) and !empty($file) and file_exists($file)) {
        $mail->addFile($file);
    }

    $mail->send();

    return;
}


/**
 * Отправка уведомления пользователю
 *
 * @param int $userId
 * @param string $subject
 * @param string $message
 * @param string|false $file
 */
 function userSendMail($userId, $subject, $message, $file = false) {
     $arUser = CUser::getFields($userId);
     $mail = new CMail;
     $mail->from('iot@'.$_SERVER['SERVER_NAME'], 'Система оповещений IoT Core');
     $mail->to($arUser['email'], $arUser['name']);
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
     if(isset($file) and !empty($file) and file_exists($file)){
         $mail->addFile($file);
     }
     $mail->sendDefault();

     return;
 }


/**
 * Преобразование массива $_FILES в более логичный вид при множественной загрузке
 *
 * @param array $arrFiles Массив $_FILES
 * @param string $name Имя поля прикрепления файла (ключ)
 *
 * @return array
 */
function prepareArrFiles($arrFiles, $name) {
    $files = array();
    foreach($arrFiles[$name] as $k => $l) {
        foreach($l as $i => $v) {
            $files[$i][$k] = $v;
        }
    }
    $arrFiles[$name] = $files;

    return $arrFiles;
}


/**
 * Преобразует байты в человекопонятный вид
 *
 * @param $bytes
 * @return string
 */
function bytesToString($bytes) {
    if ($bytes < 1000 * 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    }
    elseif ($bytes < 1000 * 1048576) {
        return number_format( $bytes / 1048576, 2) . ' MB';
    }
    elseif ($bytes < 1000 * 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    }
    else {
        return number_format($bytes / 1099511627776, 2) . ' TB';
    }
}


/**
 * Генерация GUID
 */
function generateGUID():string {
    $uid = dechex( microtime(true) * 1000 ) . bin2hex( random_bytes(8) );
    $guid = vsprintf('RG%s-1000-%s-8%.3s-%s%s%s0', str_split($uid,4));
    return strtoupper($guid);
}
