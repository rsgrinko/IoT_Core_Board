<?php
/**
*	Файл для подключения всех необходимых библиотек и первичной инициализации
*	Данный файл входит в состав системы IoT Core System
*	Разработчик: Роман Сергеевич Гринько
*	E-mail: rsgrinko@gmail.com
*	Сайт: https://it-stories.ru
*/

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
define('START_TIME', microtime(true));					// засекаем время старта скрипта
define('CORE_LOADED', true);									// флаг корректного запуска
require_once __DIR__ . '/config.php';							// подключаем конфигурационный файл
require_once DIR . '/inc/lib/Pushover.class.php';                // пуш уведомления
require_once DIR . '/inc/lib/Files.class.php';			  		// работа с файлами и папками
require_once DIR . '/inc/lib/Json.class.php';			  		// работа с json
require_once DIR . '/inc/lib/Mail.class.php';			  		// отправка почтовых сообщений
require_once DIR . '/inc/lib/Cache.class.php';			  		// кэширование
require_once DIR . '/inc/lib/DB.class.php';			  			// работа с базой данных
require_once DIR . '/inc/lib/Events.class.php';		  			// работа с событиями системы
require_once DIR . '/inc/lib/User.class.php';			  		// работа с пользователями панели
require_once DIR . '/inc/lib/Pagination.class.php';	  			// обработчик пагинации
require_once DIR . '/inc/lib/MQTT.class.php';			  		// работа с mqtt брокером
require_once DIR.'/inc/func.php';						  		// вспомогательные функции
require_once DIR . '/inc/lib/Cron.class.php';			  		// планировщик задач
require_once DIR . '/inc/lib/IoT.class.php';			  			// работа с контроллером



$DB = new DB(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME); 		// создаем объект для работы с базой данных
$DB->query('SET sql_mode = \'\'');  // сбрасываем режим работы sql_mode=only_full_group_by

IoT::init($DB); // инициализация класса работы с контроллером
Cache::init(CACHEDIR, CACHE_TTL, USE_CACHE); // инициализация модуля кэширования
if(CACHE_TYPE == 'MEMCACHE') {
    if(!Cache::useMemcache()) {
       Events::add('Ошибка при включении memcache! Используется файловое хранилище. <code>'.Cache::getLastError().'</code>', 'warning', 'cache');
    }
}



User::init($DB);	// инициализация поддержки пользователей панели
Events::init($DB);	// инициализация класса журналирования событий
Cron::init($DB);	// инициализация крона

/**
 * Массив данных о текущем пользователе
 */
$USER = [];

if(User::isUser()) {
    $cacheId = md5('CUser::getFields_'.User::$id);
    if(Cache::check($cacheId)) {
        $USER = Cache::get($cacheId);
    } else {
        $USER = User::getFields();
        unset($USER['password']);
        Cache::write($cacheId, $USER);
    }
} else {
	$USER = ['id' => 0];
}

Cron::handler(); // выполнение периодических задач на хитах

if(isset($_REQUEST['clear_cache']) and $_REQUEST['clear_cache'] =='Y') { // сброс кэша по запросу
    Cache::flush();
    Events::add('Произведена очистка кэша. Инициатор: '.$USER['login'].', ID: '.$USER['id'], 'info', 'cache');
}

$userDevices = getUserDevices($USER['id']);

//TODO: реализовать выбор устройства пользователя через панель
if($userDevices) {
    if(IoT::getSelectedDevice()) {
        $USER['deviceId'] = IoT::getSelectedDevice();
    } else {
        $USER['deviceId'] = $userDevices[0]['id'];
    }

} else {
	$USER['deviceId'] = 10;//9;										// иначе показываем демо плату
}