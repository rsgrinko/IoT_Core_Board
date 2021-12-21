<?php
/*
	Обработчик запросов от контроллера
	Данный файл входит в состав системы IoT Core System
	Разработчик: Роман Сергеевич Гринько
	E-mail: rsgrinko@gmail.com
	Сайт: https://it-stories.ru
*/
require_once __DIR__ . '/../inc/bootstrap.php';
if (!isset($_REQUEST['mac']) or $_REQUEST['mac'] == '') {     // если устройство не удалось идентифицировать - не продолжаем
    die('0000');
}

file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/controller/log.txt', print_r($_REQUEST, true));

$DEVICE['mac'] = prepareString($_REQUEST['mac']);
$DEVICE['chipid'] = prepareString($_REQUEST['chipid']);
$DEVICE['hw'] = prepareString($_REQUEST['hw']);
$DEVICE['fw'] = prepareString($_REQUEST['fw']);

if (!CIoT::isDeviceExists($DEVICE['mac'])) {                     // если устройства нет в базе
    CIoT::addDevice($DEVICE['mac'], $DEVICE['chipid'], $DEVICE['hw'], $DEVICE['fw']);  // добавляем его
}

$DEVICE['id'] = CIoT::getDeviceId($DEVICE['mac']);

CIoT::updateDeviceInfo($DEVICE['id'], $DEVICE['fw']);

/**
 * Добавляем показания датчиков DS18B20 при наличии
 */
if (isset($_REQUEST['ds']) and !empty($_REQUEST['ds'])) {            // если принята телеметрия с датчиков dallas
    foreach ($_REQUEST['ds'] as $key => $dallasSensor) {            // забиваем показания температурных датчиков
        CIoT::addDallasData($DEVICE['id'], 'ds' . ($key + 1), prepareString($dallasSensor));
    }
}

/**
 * Добавляем показания аналогого пина при наличии
 */
if (isset($_REQUEST['analog']) and !empty($_REQUEST['analog'])) {
    CIoT::addAnalogData($DEVICE['id'], prepareString($_REQUEST['analog']));
}

$DEVICE['relays'] = CIoT::getRelaysState($DEVICE['id']);

foreach ($DEVICE['relays'] as $relayState) {
    echo $relayState;
}