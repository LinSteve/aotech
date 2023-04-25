<?php
	require_once('../../config/db.php');
	
	// include('../chk_log_in.php');
		
	$sql = "Truncate log_list";
	$PDOLink->exec($sql);
	
	header('Location: ../log-frontdesk.php?success=1');
?>