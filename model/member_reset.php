<?php 

include_once('../config/db.php');

include('../chk_log_in.php');

$sql     = '';
$id      = $_GET["id"];
$type    = $_GET["type"];

$admin   = $_SESSION['admin_user']['id'];
$nowtime = date('Y-m-d H:i:s');

$default_pwd = DEF_PWD;

if($type == 'pwd') {
	$sql = "UPDATE `member` SET `password` = PASSWORD('{$default_pwd}') WHERE id = '{$id}'";
}

if($type == 'access') {
	$sql = "UPDATE `member` SET `access_password` = '{$default_pwd}', `lockmode` = '0' WHERE id = '{$id}'";
}

$flag = $PDOLink->exec($sql);

if($flag !== false) {
	
	// system_setting 
	$hw_cmd = array('op' => 'update', 'table' => 'member', 'id' => $id);
	insert_system_setting($hw_cmd);
	
	if($type == 'access') {
		
		$sql = "SELECT id FROM `room` WHERE `name` = (SELECT room_strings FROM `member` WHERE id = '{$id}')";
		$tmp = $PDOLink->prepare($sql);
		$tmp->execute();
		$rs  = $tmp->fetch();
		$room_id = $rs['id'];
		
		// 初始化
		if($room_id != '') {
			$hw_cmd = array('op' => 'Single_Initialize', 'table' => 'room', 'id' => $room_id);
			insert_system_setting($hw_cmd);			
		}
	}
	
	// log_list
	$content = "更新密碼: [member] id:{$id},  管理員:{$admin}";
	$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '', '{$nowtime}'); ";
	$PDOLink->exec($log_ins_q);
	
	header("Location: ../new-editmember.php?success=1");
	
} else {
	
	header("Location: ../new-editmember.php?error=1");
	
}

?>       