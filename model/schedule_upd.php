<?php

	require_once('../config/db.php');
	
	include('chk_log_in.php');

	$admin    = $_SESSION['admin_user']['id'];
	$nowtime  = date('Y-m-d H:i:s');
	
	$s_name   = trim($_POST["s_name"]);
	$remark   = trim($_POST["remark"]);
	$id       = $_POST['row_id'];
	$s_time   = $_POST["s_time"];
	$e_time   = $_POST["e_time"];
	$enable   = $_POST["enable"];
	$week_num = $_POST["week_num"];
	
	$sql = "SELECT * FROM `schedule` WHERE id = '{$id}'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetch();
	
	if($data == '') {
		// header('Location: curfew-editschedule.php?error=2');
		echo "<script>location.replace('../curfew-editvarious.php?id={$id}&error=2')</script>";
		exit;
	}
	
	$sql = "UPDATE `schedule` SET `name` = '{$s_name}', `remark` = '{$remark}' WHERE `id` = '{$id}'";
	$flag = $PDOLink->exec($sql);
	
	foreach($week_num as $k => $v) {
		
		$time_str  = "{";
		$time_str .= in_array($v, $enable) ? "^" : "";
		$time_str .= $s_time[$k];
		$time_str .= "~";
		$time_str .= $e_time[$k];
		$time_str .= "}";
		$nextday   = (strtotime($s_time[$k]) >= strtotime($e_time[$k])) ? '1' : '0';
		
		$sql = "UPDATE `schedule_flow` SET `time` = '{$time_str}', vision=vision+1, 
				nextday = '{$nextday}', `update_date` = '{$nowtime}' 
				WHERE `schedule_id` = '{$id}' AND `day` = '{$v}'"; // 0 -> sun
		$PDOLink->exec($sql);
	}
	
	if($flag !== false) {
		
		// log_list
		// $content = " 新增 member ::id:{$get_id}::學號(員編):{$username}::卡號:{$card_num}::姓名:{$member_name}::管理員:{$admin} ";
		// $log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', 'new', '{$nowtime}'); ";
		// $PDOLink->exec($log_ins_q);
		
		header("Location: ../curfew-editvarious.php?id={$id}&success=1");
	} else {
		header("Location: ../curfew-editvarious.php?id={$id}&error=1");
	}
	
?>