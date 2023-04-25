<?php

include_once('../config/db.php');

include('../chk_log_in.php');

$admin   = $_SESSION['admin_user']['id'];
$nowtime = date('Y-m-d H:i:s');

$sql = "UPDATE `member` SET room_strings = '', berth_number = '', update_date = '{$nowtime}' WHERE `id` != '1' AND `identity` = '0'";
$flag = $PDOLink->exec($sql);

if($flag !== false) {
	
	$hw_cmd = array('op' => 'MoveOut_All', 'table' => 'member', 'id' => '1');
	insert_system_setting($hw_cmd);
	
	// 房間初始化
	$hw_cmd = array('op' => 'ALL_Initialize', 'table' => 'room');
	insert_system_setting($hw_cmd);
	
	// 門禁初始化
	$hw_cmd = array('op' => 'ALL_Initialize', 'table' => 'door');
	insert_system_setting($hw_cmd);
	
	// 電梯初始化
	$hw_cmd = array('op' => 'ALL_Initialize', 'table' => 'elevator_hardware');
	insert_system_setting($hw_cmd);
	
	// log_list
	$content = " 全部搬出::管理員:{$admin} ";
	$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', 'import', '{$nowtime}'); ";
	$PDOLink->exec($log_ins_q);
	
	header("Location: ../new-list.php?success=2");
	
} else {
	
	header("Location: ../new-list.php?error=2");
	
}

?>