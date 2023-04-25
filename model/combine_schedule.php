<?php

	require_once('../config/db.php');
	
	$result = "";
	$tb_row = "<tr><td>%s</td><td>%s</td><td>%s</td></tr>";

	$id     = $_POST['id'];
	$sql    = "SELECT * FROM `schedule_flow` WHERE schedule_id = '{$id}'";
	$rs     = $PDOLink->prepare($sql);
	$rs->execute();
	$data   = $rs->fetchAll();
	$flow   = array();
	
	if($data == '') {
		echo $result;
		exit;
	}
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'week'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$week_arr = $rs->fetchAll();
	$week_map = array();
	
	foreach($week_arr as $v) {
		$week_map[$v['custom_id']] = $v['custom_var'];
	}
	
	foreach($data as $v) {
		$week = $v['day'];
		$flow[$week] = $v;
		if($week == 0) { $flow[7] = $v; }
	}
	
	for($i=1; $i<8; $i++) 
	{
		$week_num = $flow[$i]['day'];
		$week_str = $week_map[$week_num];
		$time_str = str_replace('{', '', str_replace('}', '', $flow[$i]['time']));
		$time_arr = explode('~', $time_str);
		$s_time_t = $time_arr[0];
		$e_time   = $time_arr[1];
		$chk_enab = strpos($s_time_t, '^') !== false;
		$s_time   = $chk_enab ? str_replace('^', '', $s_time_t) : $s_time_t;
		$enable   = $chk_enab ? "不啟用" : "啟用中";
		$show_str = $s_time.'~'.$e_time;
		
		$result  .= sprintf($tb_row, $week_str, $show_str, $enable);
	}
	
	echo $result;
?>