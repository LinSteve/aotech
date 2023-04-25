<?php  
	include_once('../../config/db.php');
	$admin = $_SESSION['admin_user']['username'];
	if(!$admin) func::alertMsg('請登入', '../session.php', true);

	$ip  = func::getUserIP(); 
	$sql = "SELECT * FROM `room` WHERE `id`=? LIMIT 1 "; 
	$data = func::excSQLwithParam('select', $sql, array($_GET["id"]), false, $PDOLink);
	if($data)
	{
		$hw_cmd = array('op' => 'Single_Initialize', 'table' => 'room', 'id' => $data['id']);
		insert_system_setting($hw_cmd);
		
		# log
		$content = "單間初始化; room_id: {$data['id']};房號: {$data['name']}; 帳號:{$admin}; ip: {$ip};"; 
		func::toLog('後台', $content, $PDOLink); 
		die(header("Location: ../power-room_initialize.php?success=1"));
	} else{
		die(header("Location: ../power-room_initialize.php?error=1"));
	};

?>       