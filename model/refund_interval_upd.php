<?php

include_once('../config/db.php');

include('../chk_log_in.php');

$id         = $_POST["id"];
$enable     = $_POST["enable"];
$weekday    = $_POST["weekday"]; // 先保留
$start_time = $_POST["start_time"];
$end_time   = $_POST["end_time"];

$time_str   = "";
$nowtime    = date('Y-m-d H:i:s');
$size_count = sizeof($start_time);

	
for($i = 0; $i < $size_count; $i++) {
	
	$time_str .= '{';
	
	if( in_array($i, $enable) ) {
		$time_str .= '^';
	}
	
	if(isset($start_time[$i])) {
		$time_str .= $start_time[$i];
	} else {
		$time_str .= '00:00';
	}
	
	$time_str .= "~";
	
	if(isset($end_time[$i])) {
		$time_str .= $end_time[$i];
	} else {
		$time_str .= '00:00';
	}
	
	$time_str .= '}';
}

$sql  = "UPDATE `refund_interval_setting` SET `time` = '{$time_str}', vision=vision+1 WHERE `id` = ".$id;
$flag = $PDOLink->exec($sql);

if($flag !== false) {

	$hw_cmd = array('op' => 'update', 'table' => 'refund_interval_setting', 'id' => $id);
	insert_system_setting($hw_cmd);
		
	// log_list
	$content = "更新指定時段: {$day_str}:{$time_str}, id = {$id}, 管理員:{$admin}";
	$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '', '{$nowtime}'); ";
	$PDOLink->exec($log_ins_q);

	header("Location: ../refund-period-edit.php?id={$id}&success=1");
	
} else {
	
	header("Location: ../refund-period-edit.php?id={$id}&error=1");
	
}

?>