<?php 
include_once('../../config/db.php');
$admin = $_SESSION['admin_user']['username'];
if(!$admin) func::alertMsg('請登入', '../session.php', true);
$ip  = func::getUserIP();

$hw_cmd = array('op' => 'ALL_Initialize', 'table' => 'room');// 全部初始化
insert_system_setting($hw_cmd);

$content = "全部初始化; 管理員:{$admin} ; ip : {$ip}";
func::toLog('後台', $content, $PDOLink); 	
	
die(header("Location: ../power-room_initialize.php?success=1"));
  
?>