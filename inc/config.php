<?php
/**
*	Главный файл конфигурации системы
*	Данный файл входит в состав системы IoT Core System
*	Разработчик: Роман Сергеевич Гринько
*	E-mail: rsgrinko@gmail.com
*	Сайт: https://it-stories.ru
*/

/**
 * Версия панели
 */
define('VERSION', '1.0.2');

/**
 * Корневая директория скрипта
 */
define('DIR', $_SERVER['DOCUMENT_ROOT']);

/**
 * Путь к публичной части скрипта
 */
define('HOME', 'https://'.$_SERVER['SERVER_NAME']);

/**
 * Использовать ли кэширование данных
 */
define('USE_CACHE', true);

/**
 * Директория хранения файлов кэша
 */
define('CACHEDIR', $_SERVER['DOCUMENT_ROOT'].'/cache/');

/**
 * Время актуальности кэша в секундах
 */
define('CACHE_TTL', 3600);

/**
 * Сервер базы данных
 */
define('DB_HOST', 'localhost');

/**
 * Логин базы данных
 */
define('DB_LOGIN', 'rsgrinko_iotcore');

/**
 * Пароль базы данных
 */
define('DB_PASSWORD', '2670135');

/**
 * Имя базы данных
 */
define('DB_NAME', 'rsgrinko_iotcore');

/**
 * Количество элементов, выводимых на страницу (для пагинации)
 */
define('PAGINATION_LIMIT', 10);

/**
 * E-Mail для уведомлений
 */
define('ADMIN_EMAIL', 'rsgrinko@yandex.ru');
