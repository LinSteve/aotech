<?php 
include_once('../../config/db.php');
$admin = $_SESSION['admin_user']['username'];
if(!$admin) func::alertMsg('請登入', '../session.php', true);

$ip = func::getUserIP(); 
//$room_id  = trim($_POST["room_id"]);
$room_num = trim($_POST["room_num"]);
$mode = trim($_POST["mode"]);
$rate = trim($_POST["price_degree"]);
$mode_220 = trim($_POST["mode_220"]);
$rate_220 = trim($_POST["price_degree_220"]);

if($mode =='' || $rate =='' || $mode_220 =='' || $rate_220 =='') die(header("Location: ../power-ratecharge.php?room_numbers_kw={$room_num}&error=1"));
//$sql = "SELECT * FROM `room` WHERE  id = ? ";
//$data = func::excSQLwithParam('select', $sql, array($room_id), false, $PDOLink);
$sql = "SELECT id FROM `room` WHERE name = ? ";
$room_id = func::excSQLwithParam('select', $sql, array($room_num), false, $PDOLink);

if($room_id['id']) 
{
	$sql = "UPDATE `room` SET `mode`= ? , `price_degree` = ? ,`mode_220` = ? , `price_degree_220` = ? , update_date=NOW() WHERE `id` = ? "; 
	$updated = func::excSQLwithParam('update', $sql, array($mode, $rate,$mode_220, $rate_220, $room_id['id']), false, $PDOLink);
	if($updated)
	{
		$hw_cmd = array('op' => 'update', 'table' => 'room', 'id' => $room_id['id'], 'field' => array('mode', 'price_degree', 'mode_220','price_degree_220'), 'mode' => 'single');
		insert_system_setting($hw_cmd);
		
		$hw_cmd = array('op' => 'Single_ChangeMode', 'table' => 'room', 'id' => $room_id['id'], 'field' => array('mode','price_degree', 'mode_220', 'price_degree_220'));
		insert_system_setting($hw_cmd);		
		$mode_chn = func:: powerMode($mode);
		$mode_chn_220 = func:: powerMode_220($mode_220);
		$content = "更新費率及模式: room_id : {$room_id['id']}; 房間名稱 : {$room_num}; 用電度數110: {$rate};收費設定110 :{$mode_chn }; 用電度數220: {$rate_220};收費設定220 :{$mode_chn_220}; 管理員:{$admin}; ip : {$ip}";
		func::toLog('後台', $content, $PDOLink);	
		die(header("Location: ../power-ratecharge.php?room_numbers_kw={$room_num}&success=1"));	
	}else{
		die(header("Location: ../power-ratecharge.php?room_numbers_kw={$room_num}&error=1"));
	}
}else{
	die(header("Location: ../power-ratecharge.php?room_numbers_kw={$room_num}&error=1"));
}
 

?>       