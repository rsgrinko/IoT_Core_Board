<?php
require_once __DIR__ . '/../inc/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

unset($USER);

if (!isset($_REQUEST['token']) or empty($_REQUEST['token'])) {
    echo Json::create(['status' => 'fail', 'message' => 'Token not set']);
    die();
}

if (!isset($_REQUEST['method']) or empty($_REQUEST['method'])) {
    echo Json::create(['status' => 'fail', 'message' => 'Method not set']);
    die();
}

$token = prepareString($_REQUEST['token']);

if (!User::isTokenExists($token)) {
    echo Json::create(['status' => 'fail', 'message' => 'Token not found']);
    die();
}

$method = prepareString($_REQUEST['method']);

$USER = User::getUserByToken($token);

// имеем пользователя, который что то хочет. действуем...
// TODO: дописать реализацию API
// pre($USER);
require_once __DIR__ . '/../inc/lib/API.class.php';

try {
    $result = API::$method();
} catch (Throwable $e) {
    echo Json::create(['status' => 'fail', 'message' => 'Метод ' . $method . ' не найден']);
    die();
}

echo Json::create($result);
    