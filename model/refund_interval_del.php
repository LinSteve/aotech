<?php

	include_once('../config/db.php');

	include('../chk_log_in.php');

	$nowtime = date('Y-m-d H:i:s');
	$admin   = $_SESSION['admin_user']['id'];

	$id  = $_GET["id"];
	$sql = "DELETE FROM `refund_interval_setting` WHERE id = '{$id}'";
	$flag = $PDOLink->exec($sql);

	if($flag) {
		
		$hw_cmd = array('op' => 'delete', 'table' => 'refund_interval_setting', 'id' => $id);
		insert_system_setting($hw_cmd);
			
		// log_list
		$content = "刪除指定時段: id = {$id}, 管理員:{$admin}";
		$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '', '{$nowtime}'); ";
		$PDOLink->exec($log_ins_q);
		
		header("Location: ../refund-period.php?success=3");
		
	} else {
		
		header("Location: ../refund-period.php?error=1");
	}
?>