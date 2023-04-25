<?php 

include_once('../config/db.php');

include('../chk_log_in.php');

$admin    = $_SESSION['admin_user']['username'];
$nowtime  = date('Y-m-d H:i:s');
$ip = func::getUserIP();
// $room_id  = $_POST["room_id"];
// $room_num = $_POST["room_num"];
$price = trim($_POST["price_elec_degree"]);
if($price == '') die(header("Location: ../rate.php?error=2"));
$sql  = "UPDATE `room` SET `price_degree` = ? ,update_date=NOW() ";
$updated = func::excSQLwithParam('update', $sql, array($price), false, $PDOLink);

if($updated) 
{ 
	// "Mode":All、Signle
	$hw_cmd = array('op' => 'update', 'table' => 'room', 'id' =>'1', 'field' => array('price_degree'), 'mode' => 'all');
	insert_system_setting($hw_cmd);
	
	// 全部初始化
	$hw_cmd = array('op' => 'ALL_ChangeMode', 'table' => 'room', 'field' => array('price_degree'));
	insert_system_setting($hw_cmd);
		
	$content = "全部房間設定[費率設定] : 費率:{$price}; ip : {$ip} ;管理員:{$admin}; ";
	func::toLog('前台', $content, $PDOLink); 
		
	die(header("Location: ../rate.php?room_numbers_kw={$room_num}&price_elec_degree={$price}&success=2"));
	
} else {
	
	die(header("Location: ../rate.php?room_numbers_kw={$room_num}&price_elec_degree={$price}&error=1"));
	
}

?>       