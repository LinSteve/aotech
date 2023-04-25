<?php
	require_once('../config/db.php');
	
	require '../vendor/autoload.php';
	
	include('../chk_log_in.php');
	
	set_time_limit(0);
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';
	
	$sql_kw      = "";
	
 	$room_num    = $_GET['room_strings'];
	$dong        = $_GET['dong'];
	$floor       = $_GET['floor'];
	
	if($room_num) { $sql_kw .= " AND `name` = '".trim($room_num)."' "; }
	if($dong)     { $sql_kw .= " AND `dong` = '{$dong}' "; }
	if($floor)    { $sql_kw .= " AND `floor` = '{$floor}' "; }
	
	$sql = "SELECT * FROM `room` WHERE 1 {$sql_kw} ORDER BY `name`";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	if($data) {

		$filename  = "用電現況匯出.csv";
		$body_date = "列印日期:".date("Ymd");
		$body_head = array("房號", "目前電表度數", "狀態");
		$body_tail = array("經手人", "", "主辦出納", "", "主辦會計", "", "機關長官", "");
		
		$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$xls = $spreadsheet->getActiveSheet();
		
		foreach($data as $k => $row) 
		{
			
			$mode       = $row['mode'];
			$status_on  = "開啟";
			$status_off = "關閉";
			$status_str = $mode == 1 ? $status_on : $status_off;
			
			if($k == 0) {
				
				$xls->setCellValueByColumnAndRow(1, $k+1, $body_date);
				
				foreach($body_head as $i => $v) {				
					$xls->setCellValueByColumnAndRow($i+1, $k+2, $v);
				}
			}
						
			$i = 1;
			
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['name']);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['amount']);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $status_str);
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
		header('Location: ../power-nowmeter.php?error=1');
	}
?>