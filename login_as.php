<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/inc/bootstrap.php';
	/*if(!CUSer::is_admin()) {
		die('403 - Access denied');
	}*/
	
	if(!isset($_REQUEST['id']) or trim($_REQUEST['id']) == '') {
		die('400 - Bad request');
	}
	$id = prepareString($_REQUEST['id']);
	
	CUser::Logout();
	session_start();
	CUser::Authorize($id);
	
	
	var_dump(CUser::is_user());
	
	header("Location: index.php");
	//die();