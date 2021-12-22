<?php
	require_once __DIR__ . '/../inc/bootstrap.php';
    unset($USER);

    if(!isset($_REQUEST['token']) or empty($_REQUEST['token'])) {
        echo CJson::create(['status' => 'fail', 'message' => 'Token not set']);
        die();
    }

    $token = prepareString($_REQUEST['token']);

    if(!CUser::isTokenExists($token)) {
        echo CJson::create(['status' => 'fail', 'message' => 'Token not found']);
        die();
    }

    $USER = CUser::getUserByToken($token);

    // имеем пользователя, который что то хочет. действуем...
    // TODO: дописать реализацию API
    pre($USER);