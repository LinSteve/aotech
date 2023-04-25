<?php

include_once('../config/db.php');
include('../chk_log_in.php');

$admin   = $_SESSION['admin_user']['id'];
$nowtime = date('Y-m-d H:i:s');

$refund_start = date('Y-m-d H:i:s', strtotime($_POST["refund_start"].' '.$_POST["refund_start_time"]));
$refund_end   = date('Y-m-d H:i:s', strtotime($_POST["refund_end"].' '.$_POST["refund_end_time"]));

$id      = '1';
$sql     = "UPDATE `system_info` SET `price_start_date` = '{$refund_start}', price_end_date='{$refund_end}' WHERE `id` = ".$id;
$flag    = $PDOLink->exec($sql);

if($flag !== false) {

	$sql  = "INSERT INTO `refund_date_logs` (`refund_start`, `refund_end`, `remark`, `created_user`, `created_at`) 
			 VALUES ('{$refund_start}', '{$refund_end}', '期末退費', '{$admin}', '{$nowtime}')";
	$flag = $PDOLink->exec($sql);
	
	$hw_cmd = array('op' => 'update', 'table' => 'system_info', 'id' => $id, 'field' => array('price_start_date', 'price_end_date'));
	insert_system_setting($hw_cmd);
		
	// log_list
	$content = "更新期末退費期間: {$refund_start} - {$refund_end}, 管理員:{$admin}";
	$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '', '{$nowtime}'); ";
	$PDOLink->exec($log_ins_q);

	header("Location: ../refund-final.php?success=1");
	
} else {
	
	header("Location: ../refund-final.php?error=1");
}
?>