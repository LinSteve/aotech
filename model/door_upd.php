<?php

	require_once('../config/db.php');
	
	include('chk_log_in.php');

	$admin    = $_SESSION['admin_user']['id'];
	$nowtime  = date('Y-m-d H:i:s');
	
	$id        = $_POST['door_id'];
	$door_name = $_POST['door_name'];
	$floor     = $_POST['floor'];
	$mode      = $_POST['mode'];
	$schedule  = $_POST['schedule'];
	
	$sql = "SELECT * FROM `door` WHERE id = '{$id}'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetch();
	
	if($data == '') {
		// header('Location: curfew-editschedule.php?error=2');
		echo "<script>location.replace('../curfew-editvarious.php?id={$id}&error=2')</script>";
		exit;
	}
	
	$sql = "UPDATE `door` SET `name` = '{$door_name}', `mode` = '{$mode}', 
			`floor` = '{$floor}', `schedule_id` = '{$schedule}' WHERE `id` = '{$id}'";
	$flag = $PDOLink->exec($sql);
	
	if($flag !== false) {
		
		// system_setting 
		$hw_cmd = array('op' => 'update', 'table' => 'door', 'id' => $id);
		insert_system_setting($hw_cmd);
		
		// 初始化 -- 20200601
		$hw_cmd = array('op' => 'Single_ChangeMode', 'table' => 'door', 'id' => $id);		
		insert_system_setting($hw_cmd);
		
		header("Location: ../curfew-editbinding.php?id={$id}&success=1");
	} else {
		header("Location: ../curfew-editbinding.php?id={$id}&error=1");
	}
?>