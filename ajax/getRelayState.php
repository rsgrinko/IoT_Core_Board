<?php
/**
 * Ajax обработчик для получения состояния каналов реле контроллера
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
	$result = [];
	
	$arRelaysState = IoT::getRelaysState($deviceId);
	
	$index = 1;
	foreach($arRelaysState as $state):
		$result['state']['relay'.$index] = ($state == '1' ? 'ВКЛЮЧЕНО' : 'ВЫКЛЮЧЕНО');
		//$result['classname']['relay'.$index] = ($state == '1' ? 'relay_state_enabled' : 'relay_state_disabled');
		$result['classname']['relay'.$index] = ($state == '1' ? 'relayon' : 'relayoff');
		
		$index++;
	endforeach;
	
	header('Content-Type: application/json');
	$result['status'] = 'ok';
	echo json_encode($result);