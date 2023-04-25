<?php 

include_once('../config/db.php');

include('../chk_log_in.php');

$admin     = $_SESSION['admin_user']['id'];
$nowtime   = date('Y-m-d H:i:s');

$id        = $_POST["id"];
$grp_name  = $_POST["grp_name"];
$grp_usage = $_POST["grp_usage"];
$remark    = $_POST["grp_remark"];

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

// for C# null 轉空 array
$door_acc_old    = array();
$elevator_old    = array();
$door_id_old     = $_POST['door_id_old'];
$elevator_id_old = $_POST['elevator_id_old'];

foreach($door_id_old as $v) {
	$door_acc_old[] = $v;
}

foreach($elevator_id_old as $v) {
	$elevator_old[] = $v;
}


$sql = ''; // avoid to rewrite permission  -- 20200428

if($_SESSION['admin_user']['username'] == WEBADMIN) {
	
	$sql = "UPDATE `group` SET `name` = '{$grp_name}', `remark` = '{$remark}', `usage` = '{$grp_usage}',
			`elevator_id` = '".json_encode($elevator)."', `door_id` = '".json_encode($door_acc)."', 
			`menu_access` = '".json_encode($menu_acc)."', 
			`update_date` = '{$nowtime}' WHERE `id` = {$id}";	
} else {
	
	$sql = "UPDATE `group` SET `name` = '{$grp_name}', `remark` = '{$remark}', `usage` = '{$grp_usage}',
			`elevator_id` = '".json_encode($elevator)."', `door_id` = '".json_encode($door_acc)."', 
			`update_date` = '{$nowtime}' WHERE `id` = {$id}";	
}

$flag = $PDOLink->exec($sql);

if($flag !== false) {
	
	// system_setting 
	$hw_cmd = array('op' => 'update', 'table' => 'group', 'id' => $id);
	insert_system_setting($hw_cmd);
	
	// 初始化 -- 20200513
	$hw_cmd = array('op' => 'GroupAuthorityModify', 'table' => 'group', 'id' => $id);
	
	if($door_acc !== $door_acc_old) {
		$hw_cmd['door_id_old'] = $door_acc_old;
	}

	if($elevator !== $elevator_old) {
		$hw_cmd['elevator_id_old'] = $elevator_old;
	}	
	
	insert_system_setting($hw_cmd);
	
	// log_list
	$content = "更新: [member] id:{$id}, 管理員:{$admin}";
	$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '{$op}', '{$nowtime}'); ";

	$PDOLink->exec($log_ins_q);
	
	header("Location: ../group_edit.php?id={$id}&success=1");
	
} else {
	
	header("Location: ../group_edit.php?id={$id}&error=1");
	
}

?>       