<?php
	require_once('../config/db.php');
	require '../vendor/autoload.php';	
	include('../chk_log_in.php');
	
	$sql_kw   = "";
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';
	
 	$cname      = $_POST['cname'];
 	$username   = $_POST['username'];
	$dong       = $_POST['dong'];
	
	$start_date = $_POST['start_date'];
	$end_date   = $_POST['end_date'];
	
	if($username) { $sql_kw .= " AND `username` = '".trim($username)."' "; }
	if($cname)    { $sql_kw .= " AND `cname` = '".trim($cname)."' "; }
	if($dong)     { $sql_kw .= " AND eh.`dong` = '{$dong}' "; }
	
	if($start_date) { 
		$s_time  = date('Y-m-d', strtotime($start_date));
		$sql_kw .= " AND er.add_date > '{$s_time}' "; 
	}
	
	if($end_date)   { 
		$e_time  = date('Y-m-d', strtotime($end_date." +1 day"));
		$sql_kw .= " AND er.add_date < '{$e_time}' "; 
	}

	$sql = "SELECT er.*, m.cname, m.username, eh.dong FROM `elevator_access_record` er 
			LEFT JOIN `elevator` e ON e.id = er.elevator_id
			LEFT JOIN `elevator_hardware` eh ON eh.id = e.elevator_hardware_id 
			LEFT JOIN `member` m ON m.id = er.member_id WHERE 1 ".$sql_kw." ORDER BY er.add_date ";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	if($data) {

		$filename  = "電梯刷卡明細匯出.csv";
		$body_date = "列印日期:".date("Ymd");
		$body_head = array("#", "刷卡時間", "棟別", "學號/姓名");
		$body_tail = array("經手人", "", "主辦出納", "", "主辦會計", "", "機關長官", "");
		
		$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$xls = $spreadsheet->getActiveSheet();
		
		foreach($data as $k => $row) 
		{
			
			$showtime  = date($date_format.' '.$time_format, strtotime($row['add_date']));
			$showdong  = $row['dong'];
			$showname  = $row['username'];
			$showname .= " / ";
			$showname .= $row['cname'];
			
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
			$xls->setCellValueByColumnAndRow($i++, $k+3, $showname);
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
		header('Location: ../curfew-lift.php?error=1');
	}
?>