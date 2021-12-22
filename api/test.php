<?php
	require_once __DIR__ . '/../inc/bootstrap.php';
    unset($USER);

    if(!isset($_REQUEST['token']) or empty($_REQUEST['token'])) {
        echo CJson::create(['status' => 'fail', 'message' => 'Token not set']);
        die();
    }
/*
    if(!isset($_REQUEST['token']) or empty($_REQUEST['token'])) {
        echo CJson::create(['status' => 'fail', 'message' => 'Token not found']);
        die();
    }*/




    $token = prepareString($_REQUEST['token']);
    $USER = CUser::getUserByToken($token);
    pre($USER);