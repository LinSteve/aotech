<?php

include_once('../config/db.php');

include('../chk_log_in.php');

$week_date   = $_POST["week_date"];
$start_time1 = $_POST["start_time1"];
$end_time1   = $_POST["end_time1"];
$enable1     = $_POST["enable1"];
$start_time2 = $_POST["start_time2"];
$end_time2   = $_POST["end_time2"];
$enable2     = $_POST["enable2"];

$time_str    = "";
$nowtime     = date('Y-m-d H:i:s');
$day_str     = date('Y/m/d', strtotime($week_date));

$time_str   .= "{";
$time_str   .= $enable1 != '' ? '^' : '';
$time_str   .= $start_time1;
$time_str   .= "~";
$time_str   .= $end_time1;
$time_str   .= "}";
$time_str   .= "{";
$time_str   .= $enable2 != '' ? '^' : '';
$time_str   .= $start_time2;
$time_str   .= "~";
$time_str   .= $end_time2;
$time_str   .= "}";

if($week_date != '' || $start_time1 != '' || $start_time2 != '' || $end_time1 != '' || $end_time2 != '') {
	
	$sql = "INSERT INTO `refund_interval_setting` (`day`, `time`, `vision`) 
			VALUES ('{$day_str}', '{$time_str}', '1')";
	$flag = $PDOLink->exec($sql);
	$new_id = $PDOLink->lastInsertId();

	$hw_cmd = array('op' => 'insert', 'table' => 'refund_interval_setting', 'id' => $new_id);
	insert_system_setting($hw_cmd);
		
	// log_list
	$content = "新增指定時段: {$day_str}:{$time_str}, id = {$new_id}, 管理員:{$admin}";
	$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '', '{$nowtime}'); ";
	$PDOLink->exec($log_ins_q);
	
	if($flag) {
		
		header("Location: ../refund-period.php?success=1");
		
	} else {
		
		header("Location: ../refund-period.php?error=1");
	}
		
} else {
	
	header("Location: ../refund-period.php?error=2");
	
}

?>