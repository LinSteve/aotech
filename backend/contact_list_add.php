<?php

	require_once('../../config/db.php');
	
	// include('chk_log_in.php');
	
	$nowtime = date('Y-m-d H:i:s');
	$enable  = '1';
	$recip   = trim($_GET['recipient']);
	$address = trim($_GET['address']);
	
	$admin   = $_SESSION['admin_user']['id'];

	$sql = "INSERT INTO `contact_list` (`recipient`, `address`, `enable`, `admin_id`, `created_at`, `updated_at`) 
			VALUES ('{$recip}', '{$address}', '{$admin}', '{$enable}', '{$nowtime}', '{$nowtime}')";
	$flag = $PDOLink->exec($sql);
			
	if($flag !== false) {
		
		// log_list
		// $content = " 新增 member ::id:{$get_id}::學號(員編):{$username}::卡號:{$card_num}::姓名:{$member_name}::管理員:{$admin} ";
		// $log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', 'new', '{$nowtime}'); ";
		// $PDOLink->exec($log_ins_q);
		
		header('Location: ../power-news.php?success=2');
	} else {
		header('Location: ../power-news.php?error=2');
	}
?>