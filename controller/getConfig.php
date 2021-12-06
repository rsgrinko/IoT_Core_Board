<?php
/**
*	Отдакт конфигурацию контроллеру по запросу
*	Данный файл входит в состав системы IoT Core System
*	Разработчик: Роман Сергеевич Гринько
*	E-mail: rsgrinko@gmail.com
*	Сайт: https://it-stories.ru
*/

	require_once __DIR__ . '/../inc/bootstrap.php';
	if(!isset($_REQUEST['mac']) or $_REQUEST['mac'] == '') {
		die('400 - Bad Request');
	}
	$mac = prepareString($_REQUEST['mac']);
	$deviceId = CIoT::getDeviceId($mac);

	header('Content-type: application/json; charset=utf-8');
	$boardConfig = CIoT::getBoardConfig($deviceId);
	$arr = array (
				'dallas_resolution' => $boardConfig['ds_resolution'],	 // разрядность датчика 9..12
				'deviceId' => $deviceId,								 // идентификатор устройства
                'sendingInterval' => $boardConfig['sending_interval'],	 // интервал обмена
				'mac' => $mac,					 					     // MAC адрес устройства
				'date' => date("d.m.y H:i:s"),
				'support' => 'rsgrinko@gmail.com'
				);
	echo json_encode($arr);