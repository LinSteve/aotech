<?php 

	// ** deprecated **
	
	$data_type = '';
	
	if(isset($_GET['data_type'])) {
		$data_type = $_GET['data_type'];
	}

	if($data_type == 'member'){

		session_start();
		unset($_SESSION);
		header("location: ../index.php"); 

	} elseif ($data_type == 'admin') {

		session_start();
		unset($_SESSION);
		header("location: ../index.php");
		
	} else {
		
		session_start();
		unset($_SESSION);
		// header("location: ../index.php");
	}
?>