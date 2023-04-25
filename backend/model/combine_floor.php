<?php
	include_once('../../config/db.php');
	$dong   = $_POST['dong'];
	$result = "<option value=''>請選擇</option>";
	if($dong != '') {	
		$sql = "SELECT floor FROM `room` WHERE dong = '{$dong}' GROUP BY floor= 'B1' desc , floor  Asc";
		$rs  = $PDOLink->prepare($sql);
		$rs->execute();
		$tmp = $rs->fetchAll();
		
		//asort($tmp);
		ksort($dong_arr);
		ksort($floor_arr);
		
		foreach($tmp as $v) {
			$floor   = $v['floor'];
			$result .= "<option value='{$floor}'>{$floor}</option>";
		} 
	} 
	echo $result;
?>
