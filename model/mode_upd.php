<?php 
include_once('../config/db.php');
include('../chk_log_in.php');

$admin    = $_SESSION['admin_user']['username'];
 
// $room_id  = $_POST["room_id"];
$room_num = trim($_POST["room_num"]);
$mode     = $_POST["mode"];
$mode_220 = $_POST["mode_220"];

$ip = func::getUserIP();
$sql = "SELECT id FROM `room` WHERE name = ? ";
$room_id = func::excSQLwithParam('select', $sql, array($room_num), false, $PDOLink);
 
if($room_id['id']) 
{
	$sql = "UPDATE `room` SET `mode` = ?,`mode_220` = ? , update_date=NOW() WHERE `id` = ? ";
	$updated = func::excSQLwithParam('update', $sql, array($mode, $mode_220, $room_id['id']), false, $PDOLink);
	if($updated) 
	{
		$hw_cmd = array('op' => 'update', 'table' => 'room', 'id' => $room_id['id'], 'field' => array('mode','mode_220'), 'mode' => 'single');
		insert_system_setting($hw_cmd);
		$hw_cmd = array('op' => 'Single_ChangeMode', 'table' => 'room', 'id' => $room_id['id'], 'field' => array('mode','mode_220'));
		insert_system_setting($hw_cmd);			
	}
 
	$mode_chn = func::powerMode($mode);
	$mode_chn_220 = func::powerMode_220($mode_220);
	$content = "個別房間設定[模式變更]: [room] id:{$room_id['id']}; 房間: {$room_num}; mode:{$mode_chn}; mode_220:{$mode_chn_220}; 管理員:{$admin}; ip: {$ip};";
	func::toLog('前台', $content, $PDOLink);  
	die(header("Location: ../charge-mode.php?room_numbers_kw={$room_num}&mode={$mode}&success=1"));
	
} else { 
	die(header("Location: ../charge-mode.php?room_numbers_kw={$room_num}&mode={$mode}&error=1"));
	
} 

?>       