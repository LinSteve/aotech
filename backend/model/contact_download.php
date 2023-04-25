<?php
	require_once('../../config/db.php');
	
	require '../../vendor/autoload.php';
	
	// include('../chk_log_in.php');
	
	set_time_limit(0);
	
	$sql_kw      = "";
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';
	
	$room_num    = $_GET['room_strings'];
	$username    = $_GET['username'];
	$start_date  = $_GET['start_date'];
	$end_date    = $_GET['end_date'];
	$status      = $_GET['status'];
	
	if($room_num) {
		$sql_kw .= " AND room_number = '{$room_num}' ";
	}

	if($username) {
		$sql_kw .= " AND username_number = '{$username}' ";
	}
	
	if($start_date) { 
		$s_time  = date('Y-m-d', strtotime($start_date));
		$sql_kw .= " AND add_date > '{$s_time}' "; 
	}
	
	if($end_date)   { 
		$e_time  = date('Y-m-d', strtotime($end_date." +1 day"));
		$sql_kw .= " AND add_date < '{$e_time}' "; 
	}
	
	if($status) {
		$sql_kw .= " AND data_type = '{$status}' "; 
	}
	
	$sql = "SELECT * FROM `content_us` 
			WHERE 1 ".$sql_kw." ORDER BY update_date DESC";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	$sql = "SELECT * FROM `custom_variables` WHERE `custom_catgory` = 'contact_status'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$status_type = $rs->fetchAll();
	$status_map  = array();
	
	foreach($status_type as $v) {
		$status_map[$v['custom_id']] = $v['custom_var'];
	}
	
	if($data) {
		
		$filename  = "客服中心紀錄匯出.csv";
		$body_date = "列印日期:".date("Ymd");
		$body_head = array("編號", "報修日期", "處理日期", "處理人員", "房號", "姓名", "學號", "電話", "e-mail", "報修-儲值主機", "報修-房內卡機", "狀態");
		$body_tail = array("經手人", "", "主辦出納", "", "主辦會計", "", "機關長官", "");
		
		$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$xls = $spreadsheet->getActiveSheet();
		
		foreach($data as $k => $row) {
			
			$showtime1 = date($date_format.' '.$time_format, strtotime($row["add_date"]));
			$showtime2 = $row['update_date'] != '' ? date($date_format.' '.$time_format, strtotime($row['update_date'])) : '';
			
			if($k == 0) {
				
				$xls->setCellValueByColumnAndRow(1, $k+1, $body_date);
				
				foreach($body_head as $i => $v) {				
					$xls->setCellValueByColumnAndRow($i+1, $k+2, $v);
				}
			}
						
			$i = 1;
			
			$xls->setCellValueByColumnAndRow($i++, $k+3, $k+1);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $showtime1);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $showtime2);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['replier']);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['room_number']);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['title']);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['username_number']);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['phone']);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['email']);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['room_type'].' '.$row['room_other']);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['host_type'].' '.$row['host_other']);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $status_map[$row['data_type']]);
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
		header('Location: ../power-stored.php?error=1');
	}
?>