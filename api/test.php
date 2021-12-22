<?php
	require_once __DIR__ . '/../inc/bootstrap.php';
    unset($USER);

    if(!isset($_REQUEST['token']) or empty($_REQUEST['token'])) {
        echo CJson::create(['status' => 'fail', 'message' => 'Token not set']);
        die();
    }

    if(!isset($_REQUEST['method']) or empty($_REQUEST['method'])) {
        echo CJson::create(['status' => 'fail', 'message' => 'Method not set']);
        die();
    }

    $token = prepareString($_REQUEST['token']);

    if(!CUser::isTokenExists($token)) {
        echo CJson::create(['status' => 'fail', 'message' => 'Token not found']);
        die();
    }

    $method = prepareString($_REQUEST['method']);

    $USER = CUser::getUserByToken($token);

    // имеем пользователя, который что то хочет. действуем...
    // TODO: дописать реализацию API
    // pre($USER);
    require_once __DIR__ . '/../inc/lib/CAPI.class.php';
    $API = new CAPI();
    try {
        $API->$method();
    } catch (Throwable $e) {
        echo CJson::create(['status' => 'fail', 'message' => 'Метод '.$method.' не найден']);
        die();
    }

    