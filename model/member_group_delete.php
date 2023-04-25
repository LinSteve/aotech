<?php
	require_once('../config/db.php');
	
	include('chk_log_in.php');
	
	$admin = $_SESSION['admin_user']['id'];
	$nowtime = date('Y-m-d H:i:s');
	
	$id    = $_GET['id'];
	
	$sql   = "UPDATE `group` SET `enable` = '0' WHERE `id` = ".$id;
	$stmt  = $PDOLink->prepare($sql);
	$flag  = $stmt->execute();
	
	if($flag !== false) {
		
		$hw_cmd = array('op' => 'update', 'table' => 'group', 'id' => $id);
		insert_system_setting($hw_cmd);
		
		// 初始化待確認 -- 20200429 
		$hw_cmd = array('op' => 'GroupDelete', 'table' => 'group', 'id' => $id);
		insert_system_setting($hw_cmd);
		
		// log_list
		$content = " 停用群組:: id:{$id}, 管理員:{$admin} ";
		$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '{$op}', '{$nowtime}') ";
		$PDOLink->exec($log_ins_q);
		
		header('Location: ../new-editgroup.php?success=4');
	} else {
		header('Location: ../new-editgroup.php?error=1');
	}
?>