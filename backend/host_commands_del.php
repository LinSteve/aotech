<?php

	require_once('../../config/db.php');
	
	// include('chk_log_in.php');

	$nowtime = date('Y-m-d H:i:s');
	$admin   = $_SESSION['admin_user']['id'];
	$id_arr  = $_POST['selected_id'];
	
	foreach($id_arr as $v) {
		$sql = "DELETE FROM system_setting WHERE id = ".$v;
		$PDOLink->exec($sql);
	}
			
	header('Location: ../develop-kiosk.php?success=1');
?>