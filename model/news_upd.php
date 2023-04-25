<?php 

include_once('../config/db.php');

include('../chk_log_in.php');

$admin    = $_SESSION['admin_user']['id'];
$nowtime  = date('Y-m-d H:i:s');

$contact = $_POST["editor1"];

$sql = "UPDATE `system_info` SET `contact` = '{$contact}' WHERE `id` = 1;";
$flag = $PDOLink->exec($sql);

if($flag !== false) {
		
	// log_list
	$content = "更新公告: contact:{$contact}, 管理員:{$admin}";
	$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '', '{$nowtime}'); ";
	$PDOLink->exec($log_ins_q);
		
	header("Location: ../news.php?success=1");
	
} else {
	
	header("Location: ../news.php?error=1");
	
}

?>       