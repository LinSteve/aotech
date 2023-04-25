<?php

	require_once('../config/db.php');
	
	include('chk_log_in.php');

	$admin   = $_SESSION['admin_user']['id'];
	$nowtime = date('Y-m-d H:i:s');
	$s_name  = trim($_POST["s_name"]);
	$remark  = trim($_POST["remark"]);
	$s_time  = $_POST["s_time"];
	$e_time  = $_POST["e_time"];
	
	$sql = "INSERT INTO `schedule` (`name`, `remark`, `add_date`, `update_date`) 
			    VALUES ('{$s_name}', '{$remark}', '{$nowtime}', '{$nowtime}')";
	$flag = $PDOLink->exec($sql);
	$last_id = $PDOLink->lastInsertId();
	
	if($last_id != '') {

		for($i=0; $i<7; $i++) {
			
			$time_str = '{'.$s_time.'~'.$e_time.'}';
			$nextday  = (strtotime($s_time) >= strtotime($e_time)) ? '1' : '0';
			
			$sql = "INSERT INTO `schedule_flow` (`schedule_id`, `day`, `time`, `vision`, `nextday`, `add_date`, `update_date`) 
					VALUES ('{$last_id}', '{$i}', '{$time_str}', '1', '{$nextday}', '{$nowtime}', '{$nowtime}')";
			$PDOLink->exec($sql);
		}
		
		if($flag !== false) {
			
			// log_list
			$content = "新增排程 :: {$s_name} ::管理員:{$admin} ";
			$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', 'new', '{$nowtime}') ";
			$PDOLink->exec($log_ins_q);
			
			header('Location: ../curfew-editvarious.php?id='.$last_id);
		} else {
			header('Location: ../curfew-addschedule.php?error=1');
		}
		
	} else {
		header('Location: ../curfew-addschedule.php?error=1');
	}
?>