<?php
/**
 * Ajax обработчик для смены состояния реле контроллера
 */
require_once __DIR__ . '/../inc/bootstrap.php';

if (!CUser::isUser()) {
    die('403 - Access denied');
}

if (!isset($_REQUEST['deviceId']) or $_REQUEST['deviceId'] == '') {
    die('400 - Bad Request');
}

$deviceId = prepareString($_REQUEST['deviceId']);

if (!isHaveAccessToDevice($deviceId, $USER['id']) and !CUser::isAdmin()) {
    die('403 - Access denied');
}

$relay = prepareString($_REQUEST['relay']);
$state = prepareString($_REQUEST['state']);

CIoT::setRelayState($deviceId, $relay, $state ? '1' : '0');