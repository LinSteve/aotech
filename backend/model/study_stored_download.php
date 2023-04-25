<?php
	require_once('../../config/db.php'); 
	require '../../vendor/autoload.php'; 
    include('../chk_log_in.php');
	
	set_time_limit(0);
	
	$sql_kw      = "";
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';
	
 	$cname       = trim($_GET['cname']);
 	$id_card    = trim($_GET['id_card']);
 	$room_num    = trim($_GET['room_strings']);
	
	$start_date  = $_GET['start_date'];
	$end_date    = $_GET['end_date'];
	$sort		 = $_GET['sort'];
	$search      = $_GET['search'];
	
	if($id_card) { $sql_kw .= " AND m.`id_card` like '%{$id_card}%' "; }
	if($cname)    { $sql_kw .= " AND m.`cname` like '%{$cname}%' "; }
	if($room_num) { $sql_kw .= " AND r.name LIKE '%{$room_num}%' "; }
	if($sort)	  { $sql_kw .= " AND r.`name` = '".trim($room_num)."' "; }
	
	if($start_date) { 
		$s_time  = date('Y-m-d', strtotime($start_date));
		$sql_kw .= " AND e.add_date > '{$s_time}' "; 
	}
	
	if($end_date)   { 
		$e_time  = date('Y-m-d', strtotime($end_date." +1 day"));
		$sql_kw .= " AND e.add_date < '{$e_time}' "; 
	}
	
	$sql = "SELECT e.*, m.username, m.cname, r.`name`, m.balance, m.berth_number ,m.id_card
			FROM `ezcard_record` e
			LEFT JOIN member m ON e.member_id = m.id
			LEFT JOIN room r ON r.id = e.room_id
			WHERE 1 AND r.Title = '研習室' ".$sql_kw." ORDER BY e.add_date DESC";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	$sort_map = array("Stored" => "付款", "Refund" => "退費");
	
	if($data) {
		
		$filename  = "學生儲值紀錄匯出.csv";
		$body_date = "列印日期:".date("Ymd");
		$body_head = array("編號", "付款日期", "姓名/卡號", "房號", "狀態", "儲值/退款後餘額", "BeforeValue", "PayValue", "SavedValue");
		$body_tail = array("經手人", "", "主辦出納", "", "主辦會計", "", "機關長官", "");
		
		$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$xls = $spreadsheet->getActiveSheet();
		
		foreach($data as $k => $row) {
			
			$showtime  = date($date_format.' '.$time_format, strtotime($row["add_date"]));
			$showname  = $row['cname'];
			$showname .= " / ";
			$showname .= $row['id_card']; 
			$showsort  = $sort_map[$row["Sort"]];
			
			if($k == 0) {
				
				$xls->setCellValueByColumnAndRow(1, $k+1, $body_date);
				
				foreach($body_head as $i => $v) {				
					$xls->setCellValueByColumnAndRow($i+1, $k+2, $v);
				}
			}
						
			$i = 1;
			
			$xls->setCellValueByColumnAndRow($i++, $k+3, $k+1);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $showtime);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $showname);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['name']);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $showsort);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['SavedValue']);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['BeforeValue']);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['PayValue']);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row['SavedValue']);
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
		header('Location: ../study-storedvalue.php?error=1');
	}
?>