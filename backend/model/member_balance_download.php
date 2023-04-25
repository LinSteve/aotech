<?php
	require_once('../../config/db.php');
	
	require '../../vendor/autoload.php';
	
	// include('../chk_log_in.php');
	
	set_time_limit(0);
	
	$sql_kw      = " AND identity = 0 ";
	$date_format = 'Y/m/d';
	$time_format = 'h:i:s';
	
 	// $cname       = $_GET['cname'];
 	$username    = $_GET['username'];
 	$room_num    = $_GET['room_strings'];
	$search      = $_GET['search'];
	
	if($username) { $sql_kw .= " AND `username` like '%".trim($username)."%' "; }
	if($cname)    { $sql_kw .= " AND `cname` like '%".trim($cname)."%' "; }
	if($room_num) { $sql_kw .= " AND concat(room_strings, berth_number) like '%".trim($room_num)."%' "; }
	if($dong)     { $sql_kw .= " AND `dong` = '{$dong}' "; }
	if($floor)    { $sql_kw .= " AND `floor` = '{$floor}' "; }
	
	$sql = "SELECT * FROM member WHERE 1 {$sql_kw} ORDER BY update_date DESC ";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	if($data) {
		
		$filename  = "餘額查詢匯出.csv";
		$body_date = "列印日期:".date("Ymd");
		$body_head = array("編號", "最新更新日", "房號/床號", "姓名", "卡號", "學號", "餘額");
		$body_tail = array("經手人", "", "主辦出納", "", "主辦會計", "", "機關長官", "");
		
		$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$xls = $spreadsheet->getActiveSheet();
		
		foreach($data as $k => $row) {
			
			$showtime = date($date_format.' '.$time_format, strtotime($row["update_date"]));
			$balance  = round($row['balance'], 1);
			
			if($k == 0) {
				
				$xls->setCellValueByColumnAndRow(1, $k+1, $body_date);
				
				foreach($body_head as $i => $v) {				
					$xls->setCellValueByColumnAndRow($i+1, $k+2, $v);
				}
			}
						
			$i = 1;
			
			$xls->setCellValueByColumnAndRow($i++, $k+3, $k+1);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $showtime);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row["room_strings"]."/".$row["berth_number"]);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['cname']);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row["id_card"]);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['username']);
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
		header('Location: ../balancesearch.php?error=1');
	}
?>