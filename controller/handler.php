<?php
/**
 *    Обработчик запросов от контроллера
 *    Данный файл входит в состав системы IoT Core System
 *    Разработчик: Роман Сергеевич Гринько
 *    E-mail: rsgrinko@gmail.com
 *    Сайт: https://it-stories.ru
 */
require_once __DIR__ . '/../inc/bootstrap.php';

// если устройство не удалось идентифицировать - не продолжаем
if (!isset($_REQUEST['mac']) or $_REQUEST['mac'] == '') {
    die('0000');
}

// для тестов логируем крайний запрос
file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/controller/log.txt', print_r($_REQUEST, true));

// предварительная обработка для защиты от SQL injection
$DEVICE['mac'] = prepareString($_REQUEST['mac']);
$DEVICE['chipid'] = prepareString($_REQUEST['chipid']);
$DEVICE['hw'] = prepareString($_REQUEST['hw']);
$DEVICE['fw'] = prepareString($_REQUEST['fw']);

// если устройство не найдено в базе - регистрируем его
if (!IoT::isDeviceExists($DEVICE['mac'])) {
    IoT::addDevice($DEVICE['mac'], $DEVICE['chipid'], $DEVICE['hw'], $DEVICE['fw']);
}

$DEVICE['id'] = IoT::getDeviceId($DEVICE['mac']);

// обновление информации об устройстве
IoT::updateDeviceInfo($DEVICE['id'], $DEVICE['fw']);

// добавляем показания датчиков DS18B20 при наличии
if (isset($_REQUEST['ds']) and !empty($_REQUEST['ds'])) {
    foreach ($_REQUEST['ds'] as $key => $dallasSensor) {
        IoT::addDallasData($DEVICE['id'], 'ds' . ($key + 1), prepareString($dallasSensor));
    }
}

// добавляем показания аналогого пина при наличии
if (isset($_REQUEST['analog']) and !empty($_REQUEST['analog'])) {
    IoT::addAnalogData($DEVICE['id'], prepareString($_REQUEST['analog']));
}

// получаем состояние каналов реле
$DEVICE['relays'] = IoT::getRelaysState($DEVICE['id']);

// отдаем контроллеру требуемые состояния реле
foreach ($DEVICE['relays'] as $relayState) {
    echo $relayState;
}