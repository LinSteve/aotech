<?php 

include_once('../config/db.php');

include('../chk_log_in.php');

$admin    = $_SESSION['admin_user']['id'];
$nowtime  = date('Y-m-d H:i:s');

$price_max  = $_POST["price_max"];

$id   = '1';
$sql  = "UPDATE `system_info` SET `price_max` = '{$price_max}' WHERE `id` = {$id}";
$flag = $PDOLink->exec($sql);

if($flag !== false) {
		
	$hw_cmd = array('op' => 'update', 'table' => 'system_info', 'id' => $id);
	insert_system_setting($hw_cmd);
		
	// log_list
	$content = "更新付費上限: price_max:{$price_max}, 管理員:{$admin}";
	$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '', '{$nowtime}'); ";
	$PDOLink->exec($log_ins_q);
		
	header("Location: ../charge-system.php?success=1");
	
} else {
	
	header("Location: ../charge-system.php?error=1");
	
}

?>       