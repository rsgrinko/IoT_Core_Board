<?php
/*
	Обработчик запросов от контроллера
	Данный файл входит в состав системы IoT Core System
	Разработчик: Роман Сергеевич Гринько
	E-mail: rsgrinko@gmail.com
	Сайт: https://it-stories.ru
*/
	file_put_contents($_SERVER['DOCUMENT_ROOT'].'/controller/new.txt', print_r($_REQUEST, true));
	//die();


	require_once __DIR__ . '/../inc/bootstrap.php';
	if(!isset($_REQUEST['mac']) or $_REQUEST['mac'] == '') {	 // если устройство не удалось идентифицировать - не продолжаем
		die('0000');
	}
	
	
	$DEVICE['mac'] = prepareString($_REQUEST['mac']);
	$DEVICE['chipid'] = prepareString($_REQUEST['chipid']);
	$DEVICE['hw'] = prepareString($_REQUEST['hw']);
	$DEVICE['fw'] = prepareString($_REQUEST['fw']);
	
	if(!CIoT::isDeviceExists($DEVICE['mac'])){ 					 // если устройства нет в базе
		CIoT::addDevice($DEVICE['mac'], $DEVICE['chipid'], $DEVICE['hw'], $DEVICE['fw']);  // добавляем его
	}
	
	$DEVICE['id'] = CIoT::getDeviceId($DEVICE['mac']);
	
	CIoT::updateDeviceInfo($DEVICE['id'], $DEVICE['fw']);
	
	if(isset($_REQUEST['ds']) and !empty($_REQUEST['ds'])){			// еели принята телеметрия с датчиков dallas
		foreach($_REQUEST['ds'] as $key => $dallasSensor){ 			// забиваем показания температурных датчиков
			CIoT::addDallasData($DEVICE['id'], 'ds'.($key+1), $dallasSensor);
		}
	}
	
	
	if(isset($_REQUEST['dht_t']) and !empty($_REQUEST['dht_t'])){
		CIoT::addDallasData($DEVICE['id'], 'dht_t', $_REQUEST['dht_t']);
		CIoT::addDallasData($DEVICE['id'], 'dht_h', $_REQUEST['dht_h']);
	}
	
	
	$DEVICE['relays'] = CIoT::getRelaysState($DEVICE['id']);
	
	foreach($DEVICE['relays'] as $relayState) {
		echo $relayState;
	}