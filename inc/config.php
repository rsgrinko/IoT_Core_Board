<?php
/*
	Главный файл конфигурации системы
	Данный файл входит в состав системы IoT Core System
	Разработчик: Роман Сергеевич Гринько
	E-mail: rsgrinko@gmail.com
	Сайт: https://it-stories.ru
*/
$CONFIG['DIR'] = $_SERVER['DOCUMENT_ROOT']; 							// корневая папка скрипта
$CONFIG['HOME'] = 'https://'.$_SERVER['SERVER_NAME'];					// путь к публичной части скрипта
$CONFIG['CACHEDIR'] = $CONFIG['DIR'].'/cache/';

$CONFIG['DB_HOST'] = 'localhost';										// Сервер базы данных
$CONFIG['DB_LOGIN'] = 'rsgrinko_iotcore';								// Логин базы данных
$CONFIG['DB_PASSWORD'] = '2670135';										// Пароль базы данных
$CONFIG['DB_NAME'] = 'rsgrinko_iotcore';								// Имя базы данных

$CONFIG['PAGINATION_LIMIT'] = 10;										// Элементов на страницу
