<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/inc/bootstrap.php';
	if(!CUser::is_admin()) {
		die('403 - Access denied');
	}
	

	$result = getClientInfo();
	
	
	
	pre($result);