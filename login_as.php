<?php
	require_once __DIR__.'/inc/bootstrap.php';
	/*if(!CUSer::is_admin()) {
		die('403 - Access denied');
	}*/
	
	if(!isset($_REQUEST['id']) or trim($_REQUEST['id']) == '') {
		die('400 - Bad request');
	}
    if(!isGod($USER['id'])) {
        die('403 - Access denied');
    }

	$id = prepareString($_REQUEST['id']);
	
	CUser::logout();
	session_start(); //TODO: нужно будет убрать при корректировке метода Logout
	CUser::authorize($id);
	
	header("Location: index.php");
	die();