<?php
/**
*	Файл для подключения всех необходимых библиотек и первичной инициализации
*	Данный файл входит в состав системы IoT Core System
*	Разработчик: Роман Сергеевич Гринько
*	E-mail: rsgrinko@gmail.com
*	Сайт: https://it-stories.ru
*/
session_start();
define('START_TIME', microtime(true));							// засекаем время старта скрипта
define('CORE_LOADED', true);									// флаг корректного запуска
require_once __DIR__ . '/config.php';							// подключаем конфигурационный файл
require_once DIR.'/inc/lib/CFiles.class.php';			  		// работа с файлами и папками
require_once DIR.'/inc/lib/CJson.class.php';			  		// работа с json
require_once DIR.'/inc/lib/CMail.class.php';			  		// отправка почтовых сообщений
require_once DIR.'/inc/lib/CCache.class.php';			  		// кэширование
require_once DIR.'/inc/lib/CDB.class.php';			  			// работа с базой данных
require_once DIR.'/inc/lib/CEvents.class.php';		  			// работа с событиями системы
require_once DIR.'/inc/lib/CUser.class.php';			  		// работа с пользователями панели
require_once DIR.'/inc/lib/CPagination.class.php';	  			// обработчик пагинации
require_once DIR.'/inc/lib/CMQTT.class.php';			  		// работа с mqtt брокером
require_once DIR.'/inc/func.php';						  		// вспомогательные функции
require_once DIR.'/inc/lib/CCron.class.php';			  		// планировщик задач
require_once DIR.'/inc/lib/CIoT.class.php';			  			// работа с контроллером


$DB = new CDB(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME); 		// создаем объект для работы с базой данных
$DB->query('SET sql_mode = \'\'');                              // сбрасываем режим работы sql_mode=only_full_group_by 

CIoT::init($DB);											    // инициализация класса работы с контроллером
CCache::init(CACHEDIR, USE_CACHE);    							// инициализация модуля кэширования

if(isset($_REQUEST['clear_cache']) and $_REQUEST['clear_cache'] =='Y') { // сброс кэша по запросу
    CCache::clearCache();
}

CUser::init($DB);												// инициализация поддержки пользователей панели
CEvents::init($DB);												// инициализация класса журналирования событий
CCron::init($DB);												// инициализация крона

if(CUser::is_user()) {
    $cacheId = md5('CUser::getFields_'.CUser::$id);
    if(CCache::checkCache($cacheId)) {
        $USER = CCache::getCache($cacheId);
    } else {
        $USER = CUser::getFields();
        CCache::writeCache($cacheId, $USER);
    }

} else {
	$USER = ['id' => 0];
}

CCron::handler();  												// выполнение периодических задач на хитах


$userDevices = getUserDevices($USER['id']);

//TODO: реализовать выбор устройства пользователя через панель
if($userDevices) {
	$USER['deviceId'] = $userDevices[0]['id']; 	 				// мониторим только первое устройство пользователя при наличии
} else {
	$USER['deviceId'] = 10;//9;										// иначе показываем демо плату
}