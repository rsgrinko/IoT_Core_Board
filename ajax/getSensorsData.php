<?php
/**
 * Ajax обработчик для получения показаний датчика DS18B20
 */
require_once __DIR__ . '/../inc/bootstrap.php';

if(!User::isUser()) {
    die('403 - Access denied');
}

if(!isset($_REQUEST['deviceId']) or $_REQUEST['deviceId'] == ''){
    die('400 - Bad Request');
}

$deviceId = prepareString($_REQUEST['deviceId']);


if(!isHaveAccessToDevice($deviceId, $USER['id']) and !User::isAdmin()) {
    die('403 - Access denied');
}

$cacheId = md5('CIoT::getSensorAllData_'.$deviceId);
if(Cache::check($cacheId) and Cache::getAge($cacheId) < 10) {
    $arSensorsData = Cache::get($cacheId);
} else {
    $arSensorsData = IoT::getSensorAllData($deviceId);
    Cache::write($cacheId, $arSensorsData);
}


header('Content-Type: application/json');
echo Json::create($arSensorsData);