<?php
	require_once('../config/db.php');
	require '../vendor/autoload.php';	
	include('../chk_log_in.php');
	
	$sql_kw      = "";
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';
	
	$cname       = $_POST['cname'];
	$username    = $_POST['username'];
	$room_num    = $_POST['room_strings'];
	$dong        = $_POST['dong'];
	$floor       = $_POST['floor'];
	$start_date  = $_POST['start_date'];
	$end_date    = $_POST['end_date'];
	$serach      = $_POST['serach'];
	
	if($cname)    { $sql_kw .= " AND m.`cname` like '%{$cname}%' "; }
	if($username) { $sql_kw .= " AND m.`username` like '%{$username}%' "; }
	if($room_num) { $sql_kw .= " AND concat(r.`name`, m.berth_number) like '%{$room_num}%' "; }
	if($dong)     { $sql_kw .= " AND r.`dong` = '{$dong}' "; }
	if($floor)    { $sql_kw .= " AND r.`floor` = '{$floor}' "; }
	
	if($start_date) { 
		$s_time  = date('Y-m-d', strtotime($start_date));
		$sql_kw .= " AND rr.add_date > '{$s_time}' "; 
	}
	
	if($end_date)   { 
		$e_time  = date('Y-m-d', strtotime($end_date." +1 day"));
		$sql_kw .= " AND rr.add_date < '{$e_time}' "; 
	}

	$sql = "SELECT rr.*, m.cname, m.username, r.dong, r.floor, r.name 
			FROM `room_access_record` rr 
			LEFT JOIN `room` r ON r.id = rr.room_id
			LEFT JOIN `member` m ON m.id = rr.member_id WHERE 1 ".$sql_kw." ORDER BY rr.add_date ";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	$access_map = array('0' => '進', '1' => '出');
	
	if($data) {

		$filename  = "房間刷卡明細匯出.csv";
		$body_date = "列印日期:".date("Ymd");
		$body_head = array("#", "刷卡時間", "棟別/樓層", "房號/床號", "學號/姓名", "備註");
		$body_tail = array("經手人", "", "主辦出納", "", "主辦會計", "", "機關長官", "");
		
		$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$xls = $spreadsheet->getActiveSheet();
		
		foreach($data as $k => $row) 
		{
			
			$showtime  = date($date_format.' '.$time_format, strtotime($row['add_date']));
			$showdong  = $row['dong'];
			$showdong .= " / ";
			$showdong .= $row['floor'];
			$showroom  = $row['name'];
			$showroom .= " / ";
			$showroom .= $row['berth_number'];
			$showname  = $row['username'];
			$showname .= " / ";
			$showname .= $row['cname'];
			$show_acs  = $access_map[$row['access']];
			
			if($k == 0) {
				
				$xls->setCellValueByColumnAndRow(1, $k+1, $body_date);
				
				foreach($body_head as $i => $v) {				
					$xls->setCellValueByColumnAndRow($i+1, $k+2, $v);
				}
			}
						
			$i = 1;
			
			$xls->setCellValueByColumnAndRow($i++, $k+3, $k+1);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $showtime);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $showdong);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $showroom);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $showname);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $show_acs);
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
		header('Location: ../curfew-room.php?error=1');
	}
?>