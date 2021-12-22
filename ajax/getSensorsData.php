<?php
/**
 * Ajax обработчик для получения показаний датчика DS18B20
 */
require_once __DIR__ . '/../inc/bootstrap.php';

if(!CUser::isUser()) {
    die('403 - Access denied');
}

if(!isset($_REQUEST['deviceId']) or $_REQUEST['deviceId'] == ''){
    die('400 - Bad Request');
}

$deviceId = prepareString($_REQUEST['deviceId']);


if(!isHaveAccessToDevice($deviceId, $USER['id']) and !CUser::isAdmin()) {
    die('403 - Access denied');
}

$cacheId = md5('CIoT::getSensorAllData_'.$deviceId);
if(CCache::check($cacheId) and CCache::getAge($cacheId) < 10) {
    $arSensorsData = CCache::get($cacheId);
} else {
    $arSensorsData = CIoT::getSensorAllData($deviceId);
    CCache::write($cacheId, $arSensorsData);
}


header('Content-Type: application/json');
echo CJson::create($arSensorsData);