<?php
require_once('../config/db.php');
 
if(!$_POST['study_area'] || !$_POST['study_mode']) die(header('Location: ../study-moderate.php?error=2')); 
$admin = $_SESSION['admin_user']['username'];

$sel_room = $_POST['study_area'];
$mode = $_POST["study_mode"];

$ip = func::getUserIP();
$in_array = str_repeat('?,', count($sel_room) -1) . '?';  // 依陣列長度加入佔位符 
$sql = "SELECT id,name FROM `room` WHERE id IN ({$in_array}) AND Title = '研習室' ";
$rooms = func::excSQLwithParam('select', $sql, $sel_room, true, $PDOLink);
$room_name = '';

if(count($rooms) > 0) 
{
	foreach($rooms as $v)
	{
		$sql = "UPDATE `room` SET `mode` = ?, update_date=NOW() WHERE `id` = ?  ";
		$update_room = func::excSQLwithParam('update', $sql, array($mode, $v['id']), false, $PDOLink);
		$sql = "UPDATE room_study_situation SET update_date=NOW() WHERE room_id = ?  "; // 更新時間
		$update_sit = func::excSQLwithParam('update', $sql, array($v['id']), false, $PDOLink);	
		if($update_room && $update_sit )
		{
			$hw_cmd = array('op' => 'update', 'table' => 'room', 'id' => $v['id'], 'field' => array('mode', 'update_date'), 'mode' => 'single');
			insert_system_setting($hw_cmd);
			$hw_cmd = array('op' => 'Single_ChangeMode', 'table' => 'room', 'id' => $v['id'], 'field' => array('mode', 'update_date'));
			insert_system_setting($hw_cmd);			
		} 
		 $room_name .= $v['name'].' ';
		 $mode_chn = func::powerMode($mode); 
	}   
		$content = "研習室模式變更: [room]   房間: {$room_name}; mode:{$mode_chn}, 管理員:{$admin}; ip: {$ip};";
		func::toLog('前台', $content, $PDOLink);  
		die(header('Location: ../study-moderate.php?success=1'));	
} else { 
	die(header('Location: ../study-moderate.php?error=2'));
} 

?>