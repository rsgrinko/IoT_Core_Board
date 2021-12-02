<?php
/**
*	Главный файл конфигурации системы
*	Данный файл входит в состав системы IoT Core System
*	Разработчик: Роман Сергеевич Гринько
*	E-mail: rsgrinko@gmail.com
*	Сайт: https://it-stories.ru
*/

/**
 * Корневая директория скрипта
 */
$CONFIG['DIR'] = $_SERVER['DOCUMENT_ROOT'];

/**
 * Путь к публичной части скрипта
 */
$CONFIG['HOME'] = 'https://'.$_SERVER['SERVER_NAME'];

/**
 * Директория хранения файлов кэша
 */
$CONFIG['CACHEDIR'] = $CONFIG['DIR'].'/cache/';

/**
 * Сервер базы данных
 */
$CONFIG['DB_HOST'] = 'localhost';

/**
 * Логин базы данных
 */
$CONFIG['DB_LOGIN'] = 'rsgrinko_iotcore';

/**
 * Пароль базы данных
 */
$CONFIG['DB_PASSWORD'] = '2670135';

/**
 * Имя базы данных
 */
$CONFIG['DB_NAME'] = 'rsgrinko_iotcore';

/**
 * Количество элементов, выводимых на страницу (для пагинации)
 */
$CONFIG['PAGINATION_LIMIT'] = 10;
