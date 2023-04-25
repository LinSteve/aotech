<?php
	require_once('../../config/db.php');
	
	// include('../chk_log_in.php');
		
	$sql = "Truncate room_hardware_initialization";
	$PDOLink->exec($sql);
	
	header('Location: ../log-initialize.php?success=1');
?>