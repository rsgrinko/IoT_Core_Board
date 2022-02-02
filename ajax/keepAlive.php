<?php
/**
 * Поддержание авторизации пользователя
 */
	require_once __DIR__ . '/../inc/bootstrap.php';
	
    $result = [];
	if(!User::isUser()) {
		$result = ['status' => 'fail', 'error' => '403 - Access denied'];
	} else {
        $result = ['status' => 'ok'];
    }

    echo Json::create($result);