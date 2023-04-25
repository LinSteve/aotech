<?php
	require_once('../config/db.php');
	
	include('chk_log_in.php');

	$admin   = $_SESSION['admin_user']['id'];
	$nowtime = date('Y-m-d H:i:s');
	
	$id  = $_GET['id'];
	
	$sql = "SELECT * FROM `member` WHERE id = '{$id}'";
	$tmp = $PDOLink->prepare($sql);
	$tmp->execute();
	$rs  = $tmp->fetch();
	
	$room_id   = '';
	$member_id = $rs['id'];
	$room_num  = $rs['room_strings'];
	
	if($member_id == '') {
		header("location: ../new-editmember.php?error=2");
		return;
	}
	
	if($room_num != '') {
		
		$sql = "SELECT * FROM `room` WHERE `name` = '{$room_num}'";
		$tmp = $PDOLink->prepare($sql);
		$tmp->execute();
		$rs  = $tmp->fetch();
		
		$room_id = $rs['id'];
	}
	
	// 離宿
	// 離宿房、床號清空、卡號不歸零
	$sql  = "UPDATE `member` SET `del_mark` = '1', `room_strings` = '', `berth_number` = '' WHERE `id` = {$id}";
	$stmt = $PDOLink->prepare($sql);
	$flag = $stmt->execute();	
	
	if($flag !== false) {
		
		// system_setting 
		$hw_cmd = array('op' => 'update', 'table' => 'member', 'id' => $id);
		insert_system_setting($hw_cmd);
		
		// 群組變更初始化 弘光才有-- 20200902
		// $hw_cmd = array('op' => 'MemberGroup_Initialize', 'table' => 'member', 'id' => $id);
		// insert_system_setting($hw_cmd);
		
		// 房間初始化
		if($room_id != '') {
			$hw_cmd = array('op' => 'Single_Initialize', 'table' => 'room', 'id' => $room_id);
			insert_system_setting($hw_cmd);
		}
		
		// log_list
		$content = " 離宿: [member] id:{$id}, 管理員:{$admin} ";
		$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '{$op}', '{$nowtime}'); ";
		$PDOLink->exec($log_ins_q);
		
		header('Location: ../new-editmember.php?success=4');
	} else {
		header('Location: ../new-editmember.php?error=1');
	}
?>