<?php 

include_once('../../config/db.php');

// include('../chk_log_in.php');

$room_id  = $_GET["id"];
$reset_id  = $_GET["reset_id"];
$admin    = $_SESSION['admin_user']['id'];
$nowtime  = date('Y-m-d H:i:s');

// 硬體命令-顯示在SQL Log、system-setting
if($room_id != '') {
	$sql = "SELECT * FROM reset_hardware_info WHERE id=? LIMIT 1";
	echo 'reset_id:'.$reset_id."<BR>";
	echo 'sql:'.$sql."<BR>";
	//die();
	$room_data = func::excSQLwithParam('select', $sql, array($reset_id), false, $PDOLink);
	
	if($room_data) {
		$dongs = explode(",", $room_data['description']);
		$dong = $dongs[0];
		$sql = "SELECT * FROM room WHERE name=? LIMIT 1";
		$dong_data = func::excSQLwithParam('select', $sql, array($dong), false, $PDOLink);
		if($dong_data) {
			echo 'dong:'.$dong_data['dong'].'<BR>';
			//die();
			$hw_cmd = array('op' => 'Reset_Hardware', 'table' => 'member', 'id' => $room_id);
			insert_system_setting_for_dong($hw_cmd, $dong_data['dong']);
		} else {
			die(header('Location: ../system-reset.php?error=3')); // 房號對應棟別不存在,無法執行重啟指令
		}
	} else {
		die(header('Location: ../system-reset.php?error=2')); // 棟別不存在,無法執行重啟指令
	}
}

// log_list
$content = "後台硬體系統重啟, id:{$room_id}, 管理員:{$admin}";
$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '', '{$nowtime}'); ";
$PDOLink->exec($log_ins_q);

// OLD-後台單間門禁初始化
// if($room_id != '') {
// 	$hw_cmd = array('op' => 'Single_Initialize', 'table' => 'door', 'id' => $room_id);
// 	insert_system_setting($hw_cmd);			
// }
// OLD-log_list
// $content = "後台單間門禁初始化, id:{$room_id}, 管理員:{$admin}";
// $log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '', '{$nowtime}'); ";
// $PDOLink->exec($log_ins_q);

header("Location: ../system-reset.php?success=1");

?>