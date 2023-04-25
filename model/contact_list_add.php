<?php

	require_once('../config/db.php');
	
	include('chk_log_in.php');
	
	$enable  = '1';
	$recip   = trim($_GET['recipient']);
	$address = trim($_GET['address']);
	$nowtime = date('Y-m-d H:i:s');
	$admin   = $_SESSION['admin_user']['id'];

	$sql = "INSERT INTO `contact_list` (`recipient`, `address`, `enable`, `admin_id`, `created_at`, `updated_at`) 
			VALUES ('{$recip}', '{$address}', '{$enable}', '{$admin}', '{$nowtime}', '{$nowtime}')";
	$flag = $PDOLink->exec($sql);
			
	if($flag !== false) {
		
		header('Location: ../news.php?success=2');
	} else {
		header('Location: ../news.php?error=2');
	}
?>