<?php

	require_once('../config/db.php');
	
	include('chk_log_in.php');
	
	$now_time = date('Y-m-d H:i:s');
	
	for($i=1; $i<=27; $i++) {
		$remark = '';
		$dong = 'Q';
		$floor = '2';
		$block = '1';
		$room_no = str_pad($i, 2, '0', STR_PAD_LEFT);
		$sql = "INSERT INTO `room` (`id`, `name`, `center_id`, `meter_id`, `mode`, `price_degree`, `amount`, `dong`, `floor`, `update_date`, `add_date`, `Title`) 
				VALUES (NULL, '{$dong}{$floor}{$block}{$room_no}', '1', '1', '1', '4.5', '0', '{$dong}', '{$floor}', '{$now_time}', '{$now_time}', '{$remark}')";
		$PDOLink->exec($sql);		
	}
	
	for($i=1; $i<=15; $i++) {
		$remark = '';
		$dong = 'Q';
		$floor = '2';
		$block = '2';
		$room_no = str_pad($i, 2, '0', STR_PAD_LEFT);
		$sql = "INSERT INTO `room` (`id`, `name`, `center_id`, `meter_id`, `mode`, `price_degree`, `amount`, `dong`, `floor`, `update_date`, `add_date`, `Title`) 
				VALUES (NULL, '{$dong}{$floor}{$block}{$room_no}', '1', '1', '1', '4.5', '0', '{$dong}', '{$floor}', '{$now_time}', '{$now_time}', '{$remark}')";
		$PDOLink->exec($sql);		
	}
	
	for($i=1; $i<=27; $i++) {
		$remark = '';
		$dong = 'Q';
		$floor = '3';
		$block = '1';
		$room_no = str_pad($i, 2, '0', STR_PAD_LEFT);
		$sql = "INSERT INTO `room` (`id`, `name`, `center_id`, `meter_id`, `mode`, `price_degree`, `amount`, `dong`, `floor`, `update_date`, `add_date`, `Title`) 
				VALUES (NULL, '{$dong}{$floor}{$block}{$room_no}', '1', '1', '1', '4.5', '0', '{$dong}', '{$floor}', '{$now_time}', '{$now_time}', '{$remark}')";
		$PDOLink->exec($sql);		
	}
	
	for($i=1; $i<=15; $i++) {
		$remark = '';
		$dong = 'Q';
		$floor = '3';
		$block = '2';
		$room_no = str_pad($i, 2, '0', STR_PAD_LEFT);
		$sql = "INSERT INTO `room` (`id`, `name`, `center_id`, `meter_id`, `mode`, `price_degree`, `amount`, `dong`, `floor`, `update_date`, `add_date`, `Title`) 
				VALUES (NULL, '{$dong}{$floor}{$block}{$room_no}', '1', '1', '1', '4.5', '0', '{$dong}', '{$floor}', '{$now_time}', '{$now_time}', '{$remark}')";
		$PDOLink->exec($sql);		
	}
	
	for($i=1; $i<=27; $i++) {
		$remark = '';
		$dong = 'Q';
		$floor = '4';
		$block = '1';
		$room_no = str_pad($i, 2, '0', STR_PAD_LEFT);
		$sql = "INSERT INTO `room` (`id`, `name`, `center_id`, `meter_id`, `mode`, `price_degree`, `amount`, `dong`, `floor`, `update_date`, `add_date`, `Title`) 
				VALUES (NULL, '{$dong}{$floor}{$block}{$room_no}', '1', '1', '1', '4.5', '0', '{$dong}', '{$floor}', '{$now_time}', '{$now_time}', '{$remark}')";
		$PDOLink->exec($sql);		
	}
	
	for($i=1; $i<=15; $i++) {
		$remark = '';
		$dong = 'Q';
		$floor = '4';
		$block = '2';
		$room_no = str_pad($i, 2, '0', STR_PAD_LEFT);
		$sql = "INSERT INTO `room` (`id`, `name`, `center_id`, `meter_id`, `mode`, `price_degree`, `amount`, `dong`, `floor`, `update_date`, `add_date`, `Title`) 
				VALUES (NULL, '{$dong}{$floor}{$block}{$room_no}', '1', '1', '1', '4.5', '0', '{$dong}', '{$floor}', '{$now_time}', '{$now_time}', '{$remark}')";
		$PDOLink->exec($sql);		
	}
	
	for($i=1; $i<=27; $i++) {
		$remark = '';
		$dong = 'Q';
		$floor = '5';
		$block = '1';
		$room_no = str_pad($i, 2, '0', STR_PAD_LEFT);
		$sql = "INSERT INTO `room` (`id`, `name`, `center_id`, `meter_id`, `mode`, `price_degree`, `amount`, `dong`, `floor`, `update_date`, `add_date`, `Title`) 
				VALUES (NULL, '{$dong}{$floor}{$block}{$room_no}', '1', '1', '1', '4.5', '0', '{$dong}', '{$floor}', '{$now_time}', '{$now_time}', '{$remark}')";
		$PDOLink->exec($sql);		
	}
	
	for($i=1; $i<=15; $i++) {
		$remark = '';
		$dong = 'Q';
		$floor = '5';
		$block = '2';
		$room_no = str_pad($i, 2, '0', STR_PAD_LEFT);
		$sql = "INSERT INTO `room` (`id`, `name`, `center_id`, `meter_id`, `mode`, `price_degree`, `amount`, `dong`, `floor`, `update_date`, `add_date`, `Title`) 
				VALUES (NULL, '{$dong}{$floor}{$block}{$room_no}', '1', '1', '1', '4.5', '0', '{$dong}', '{$floor}', '{$now_time}', '{$now_time}', '{$remark}')";
		$PDOLink->exec($sql);		
	}
	
	for($i=1; $i<=27; $i++) {
		$remark = '';
		$dong = 'Q';
		$floor = '6';
		$block = '1';
		$room_no = str_pad($i, 2, '0', STR_PAD_LEFT);
		$sql = "INSERT INTO `room` (`id`, `name`, `center_id`, `meter_id`, `mode`, `price_degree`, `amount`, `dong`, `floor`, `update_date`, `add_date`, `Title`) 
				VALUES (NULL, '{$dong}{$floor}{$block}{$room_no}', '1', '1', '1', '4.5', '0', '{$dong}', '{$floor}', '{$now_time}', '{$now_time}', '{$remark}')";
		$PDOLink->exec($sql);		
	}
	
	for($i=1; $i<=13; $i++) {
		$remark = 'VIP房';
		$dong = 'Q';
		$floor = '6';
		$block = '2';
		$room_no = str_pad($i, 2, '0', STR_PAD_LEFT);
		$sql = "INSERT INTO `room` (`id`, `name`, `center_id`, `meter_id`, `mode`, `price_degree`, `amount`, `dong`, `floor`, `update_date`, `add_date`, `Title`) 
				VALUES (NULL, '{$dong}{$floor}{$block}{$room_no}', '1', '1', '1', '4.5', '0', '{$dong}', '{$floor}', '{$now_time}', '{$now_time}', '{$remark}')";
		$PDOLink->exec($sql);
	}
	
	$sql = "UPDATE `room` SET `Title` = '女預留寢室' WHERE `name` = 'Q6102';";
	$sql.= "UPDATE `room` SET `Title` = '男預留寢室' WHERE `name` = 'Q3102';";
	$sql.= "UPDATE `room` SET `Title` = '無障礙寢室' WHERE `name` = 'Q2202';";
	$sql.= "UPDATE `room` SET `Title` = '無障礙寢室' WHERE `name` = 'Q3202';";
	$sql.= "UPDATE `room` SET `Title` = '無障礙寢室' WHERE `name` = 'Q4202';";
	$sql.= "UPDATE `room` SET `Title` = '無障礙寢室' WHERE `name` = 'Q5202';";
	$PDOLink->exec($sql);
?>