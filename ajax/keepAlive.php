<?php
/**
 * Поддержание авторизации пользователя
 */
	require_once __DIR__ . '/../inc/bootstrap.php';
	
    $result = [];
	if(!CUser::isUser()) {
		$result = ['status' => 'fail', 'error' => '403 - Access denied'];
	} else {
        $result = ['status' => 'ok'];
    }

    echo CJson::create($result);