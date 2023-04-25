<?php 

include_once('../config/db.php');

include('../chk_log_in.php');

$admin  = $_SESSION['admin_user']['username']; 
$ip = func::getUserIP();
$mode  = trim($_POST["mode"]); 
if($mode == '' ) die(header("Location: ../charge-mode.php?error=2"));

$sql  = "UPDATE `room` SET  `mode` = ? ,update_date=NOW()";
$updated = func::excSQLwithParam('update', $sql, array($mode), false, $PDOLink);

if($updated) 
{
	$hw_cmd = array('op' => 'update', 'table' => 'room', 'id' => '1', 'field' => array('mode'), 'mode' => 'all');
	insert_system_setting($hw_cmd); 
	$hw_cmd = array('op' => 'ALL_ChangeMode', 'table' => 'room', 'field' => array('mode'));
	insert_system_setting($hw_cmd); 
	
	$mode_chn = func::powerMode($mode);
	$content = "全部房間設定[模式變更]: 收費設定:{$mode_chn } ; ip : {$ip} ; 管理員 : {$admin};  ";
	func::toLog('前台', $content, $PDOLink);  
	die(header("Location: ../charge-mode.php?success=2"));
	
} else { 
	die(header("Location: ../charge-mode.php?error=1"));
	
}
?>       