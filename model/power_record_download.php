<?php
	require_once('../config/db.php');
	require '../vendor/autoload.php';	
	include('../chk_log_in.php');
	
	set_time_limit(0);
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';
	
	$data1;
	$data2;
	$sql         = "";
	$sql_kw      = "";
	$def_in_use  = "使用中";
	
 	$cname       = $_GET['cname'];
 	$username    = $_GET['username'];
 	$room_num    = $_GET['room_strings'];
	
	$dong        = $_GET['dong'];
	$floor       = $_GET['floor'];
	$start_date  = $_GET['start_date'];
	$end_date    = $_GET['end_date'];
	$status		 = $_GET['status'];
	
	if($username) { $sql_kw .= " AND `username` like '%".trim($username)."%' "; }
	if($cname)    { $sql_kw .= " AND `cname` like '%".trim($cname)."%' "; }
	if($room_num) { $sql_kw .= " AND  concat(r.name, m.berth_number) LIKE '%".trim($room_num)."%' "; }
	if($dong)     { $sql_kw .= " AND r.dong = '{$dong}' "; }
	if($floor)    { $sql_kw .= " AND r.floor = '{$floor}' "; }
		
	if($status != '2') {
		$sql_dt = "";
		
		if($start_date) { 
			$s_time  = date('Y-m-d', strtotime($start_date));
			$sql_dt .= " AND res.start_date > '{$s_time}' "; 
		}
		
		if($end_date)   { 
			$e_time  = date('Y-m-d', strtotime($end_date." +1 day"));
			$sql_dt .= " AND res.start_date < '{$e_time}' "; 
		}
		
		$sql = "SELECT res.start_date, '{$def_in_use}' as 'end_date', res.powerstaus as 'power_status',
				res.start_amonut as 'start_amount', '{$def_in_use}' as 'end_amount', '' as start_balance, '' as end_balance,
				m.username, m.cname, '{$def_in_use}' as `balance`, r.dong, r.floor, r.name, m.berth_number 
				FROM `room_electric_situation` res
				LEFT JOIN `member` m ON m.id = res.member_id 
				LEFT JOIN `room` r ON r.id = res.room_id
				WHERE res.powerstaus = 1 AND r.Title<>'研習室' {$sql_kw}{$sql_dt} ORDER BY start_date DESC";
		$rs  = $PDOLink->prepare($sql);
		$rs->execute();
		$data1 = $rs->fetchAll();
	} 
	
	if($status != '1') {
		$sql_dt = "";
		
		if($start_date) { 
			$s_time  = date('Y-m-d', strtotime($start_date));
			$sql_dt .= " AND rer.start_date > '{$s_time}' "; 
		}
		
		if($end_date)   { 
			$e_time  = date('Y-m-d', strtotime($end_date." +1 day"));
			$sql_dt .= " AND rer.start_date < '{$e_time}' "; 
		}
		
		$sql = "SELECT rer.start_date, rer.end_date, '0' as 'power_status', 
				rer.start_amount, rer.end_amount, 
				rer.start_balance, rer.end_balance, m.username, m.cname, 
				IFNULL(rer.end_balance, 0) as `balance`, r.dong, r.floor, r.name, m.berth_number 
				FROM `room_electric_record` rer
				LEFT JOIN `member` m ON m.id = rer.member_id 
				LEFT JOIN `room` r ON r.id = rer.room_id
				WHERE 1 AND r.Title<>'研習室' {$sql_kw}{$sql_dt} ORDER BY start_date DESC";
		$rs  = $PDOLink->prepare($sql);
		$rs->execute();
		$data2 = $rs->fetchAll();
		$rs  = $PDOLink->prepare($sql);
		$rs->execute();
		$data2 = $rs->fetchAll();
	}
	
	if($status == '1') {
		$data0 = $data1;
	} else if($status == '2') {
		$data0 = $data2;
	} else {
		foreach($data1 as $v) {
			$data0[] = $v;
		}
		
		foreach($data2 as $v) {
			$data0[] = $v;
		}
	}
	
	$data = $data0;
	
	if($data) {
		
		$filename  = "電力使用紀錄匯出.csv";
		$body_date = "列印日期:".date("Ymd");
		$body_head = array("編號", "開始用電時間", "結束用電時間", "開始度數 ~ 結束度數", "棟別/樓層", "房號/床號", "學號/姓名", "電費金額", "餘額");
		$body_tail = array("經手人", "", "主辦出納", "", "主辦會計", "", "機關長官", "");
		
		$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$xls = $spreadsheet->getActiveSheet();
		
		foreach($data as $k => $row) {
			
			$s_time    = date($date_format.' '.$time_format, strtotime($row['start_date']));
			$e_time    = $row['end_date'] == $def_in_use ? $def_in_use : date($date_format.' '.$time_format, strtotime($row['end_date']));
			
			$show_amt  = $row['start_amount'];
			$show_amt .= " ~ ";
			$show_amt .= $row['end_amount'];
			
			$showname  = $row['username'];
			$showname .= " / ";
			$showname .= $row['cname'];
			
			$building  = $row['dong'];
			$building .= " / ";
			$building .= $row['floor'];
			
			$powersts  = $row["power_status"];
			
			if($powersts == 1) {
				$elec_fee  = $def_in_use;
				$balance   = $def_in_use;
				$b_style   = "";

			} else {
				$elec_fee  = round($row['start_balance'] - $row['end_balance'], 1);
				$balance   = round($row['balance'], 1);
				$b_style   = ($row['balance'] < 0) ? "style='color:red'" : "";
			}
			
			if($k == 0) {
				
				$xls->setCellValueByColumnAndRow(1, $k+1, $body_date);
				
				foreach($body_head as $i => $v) {				
					$xls->setCellValueByColumnAndRow($i+1, $k+2, $v);
				}
			}
						
			$i = 1;
			
			$xls->setCellValueByColumnAndRow($i++, $k+3, $k+1);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $s_time);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $e_time);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $show_amt);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $building);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row["name"].'/'.$row["berth_number"]);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $showname);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $elec_fee);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $balance);
		}
		
		foreach($body_tail as $i => $v) {				
			$xls->setCellValueByColumnAndRow($i+1, $k+5, $v);
		}
		
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Csv');
		$writer->setUseBOM(true);
		$writer->save('php://output');
		
	} else {
		header('Location: ../power-record.php?error=1');
	}
?>