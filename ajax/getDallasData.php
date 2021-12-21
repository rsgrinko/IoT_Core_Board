<?php
/**
 * Ajax обработчик для получения показаний датчика DS18B20
 */
	require_once __DIR__ . '/../inc/bootstrap.php';

	if(!CUser::isUser()) {
		die('403 - Access denied');
	}
	
	if(!isset($_REQUEST['deviceId']) or $_REQUEST['deviceId'] == '' or !isset($_REQUEST['sensor']) or $_REQUEST['sensor'] == ''){
		die('400 - Bad Request');
	}
	
	$deviceId = prepareString($_REQUEST['deviceId']);
	$sensor = prepareString($_REQUEST['sensor']);
	
	
	if(!isHaveAccessToDevice($deviceId, $USER['id']) and !CUser::isAdmin()) {
		die('403 - Access denied');
	}

    $cacheId = md5('CIoT::getSensorData_'.$deviceId.'_'.$sensor);
    if(CCache::check($cacheId) and CCache::getAge($cacheId) < 10) {
        $arDallasData = CCache::get($cacheId);
    } else {
        $arDallasData = CIoT::getSensorData($deviceId, $sensor);
        CCache::write($cacheId, $arDallasData);
    }

	
	$result = array('sensor' => $arDallasData['sensor'], 'value' => $arDallasData['value']);
	
	
	header('Content-Type: application/json');
	echo json_encode($result);