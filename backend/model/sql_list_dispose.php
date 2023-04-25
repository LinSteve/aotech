<?php
	require_once('../../config/db.php');
	
	// include('../chk_log_in.php');
		
	$sql = "Truncate system_setting";
	$PDOLink->exec($sql);
	
	header('Location: ../log-sql.php?success=1');
?>