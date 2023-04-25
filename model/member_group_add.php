<?php
	require_once('../config/db.php');
	
	include('chk_log_in.php');
	
	$admin     = $_SESSION['admin_user']['id'];
	$nowtime   = date('Y-m-d H:i:s');
	
	$groupname = $_POST['groupname'];
	$usage     = $_POST['usage'];
	$remark    = $_POST['remark'];
	
	$opt1      = $_POST['opt1'];
	$opt2      = $_POST['opt2'];
	$opt3      = $_POST['opt3'];
	$opt4      = $_POST['opt4'];
	$opt5      = $_POST['opt5'];
	$opt6      = $_POST['opt6'];
	
	$menu_acc  = array();
	$door_acc  = array();
	$elevator  = array();
	$room_acc  = array();
	
	foreach($opt1 as $v) {
		$menu_acc[] = $v;
	}
	
	foreach($opt2 as $v) {
		$menu_acc[] = $v;
	}
	
	foreach($opt3 as $v) {
		$menu_acc[] = $v;
	}
	
	foreach($opt4 as $v) {
		$door_acc[] = $v;
	}
	
	foreach($opt5 as $v) {
		$elevator[] = $v;
	}
	
	foreach($opt6 as $v) {
		$room_acc[] = $v;
	}
	
	$sql = "
		INSERT INTO `group` (`name`, `room_id`, `elevator_id`, `door_id`, 
		`menu_access`, `enable`, `usage`, `remark`, `add_date`, `update_date`) VALUES (
		'{$groupname}', '".json_encode($room_acc)."', '".json_encode($elevator)."', '".json_encode($door_acc)."', 
		'".json_encode($menu_acc)."', '1', '{$usage}', '{$remark} ', '{$nowtime}', '{$nowtime}')";		
	$stmt = $PDOLink->prepare($sql);
	$flag = $stmt->execute();
	$get_id = $PDOLink->lastInsertId();
	
	if($flag !== false) {
		
		$hw_cmd = array('op' => 'insert', 'table' => 'group', 'id' => $get_id);
		insert_system_setting($hw_cmd);
		
		// 初始化待確認 -- 20200429
		// $hw_cmd = array('op' => 'Single_Initialize', 'table' => '???', 'id' => $id);
		// insert_system_setting($hw_cmd);
		
		// log_list
		$content = " 新增 group :: id:{$get_id}, 群組名稱:{$groupname}, 管理員:{$admin} ";
		$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '{$op}', '{$nowtime}') ";
		$PDOLink->exec($log_ins_q);
		
		header('Location: ../new-group.php?success=1');
	} else {
		header('Location: ../new-group.php?error=1');
	}
	
?>