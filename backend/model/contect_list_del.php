<?php

	require_once('../../config/db.php');
	
	// include('chk_log_in.php');

	$nowtime = date('Y-m-d H:i:s');

	$enable  = '1';
	$recip   = $_GET['id'];
	$admin   = $_SESSION['admin_user']['id'];

	$sql  = "UPDATE `contact_list` SET `enable` = '0', admin_id = '{$admin}', updated_at = '{$nowtime}' WHERE `id` = ".$recip;
	$flag = $PDOLink->exec($sql);
			
	if($flag !== false) {
		
		// log_list
		// $content = " 新增 member ::id:{$get_id}::學號(員編):{$username}::卡號:{$card_num}::姓名:{$member_name}::管理員:{$admin} ";
		// $log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', 'new', '{$nowtime}'); ";
		// $PDOLink->exec($log_ins_q);
		
		header('Location: ../power-news.php?success=1');
	} else {
		header('Location: ../power-news.php?error=1');
	}
?>