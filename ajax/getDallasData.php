<?php
	require_once __DIR__ . '/../inc/bootstrap.php';
	//pre($_SESSION);
	if(!CUser::is_user()) {
		die('403 - Access denied');
	}
	
	if(!isset($_REQUEST['deviceId']) or $_REQUEST['deviceId'] == '' or !isset($_REQUEST['sensor']) or $_REQUEST['sensor'] == ''){
		die('400 - Bad Request');
	}
	
	$deviceId = prepareString($_REQUEST['deviceId']);
	$sensor = prepareString($_REQUEST['sensor']);
	
	
	if(!isHaveAccessToDevice($deviceId, $USER['id'])) {
		die('403 - Access denied');
	}

	$arDallasData = CIoT::getDallasData($deviceId, $sensor);
	
	$result = array('sensor' => $arDallasData['sensor'], 'value' => $arDallasData['value']);
	
	
	header('Content-Type: application/json');
	echo json_encode($result);