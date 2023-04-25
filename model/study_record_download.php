<?php
	require_once('../config/db.php');
	require '../vendor/autoload.php';	
	include('../chk_log_in.php');
	ini_set('max_execution_time', 0); // 最大連線逾時時間 (在php安全模式下無法使用)
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';
	
	$data1;
	$data2;
	$sql         = "";
	$sql_kw      = "";
	$def_in_use  = "使用中";
	$max_col = 30000; //最大匯出列數
	
	
 	$cname       = trim($_GET['cname']);
 	$id_card    = trim($_GET['id_card']);
 	$room_num    = trim($_GET['room_strings']);
	
	// $dong        = $_GET['dong'];
	// $floor       = $_GET['floor'];
	$start_date  = $_GET['start_date'];
	$end_date    = $_GET['end_date'];
	$status		 = $_GET['status'];

	$PDOLink -> setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false); // 非緩衝查詢
	
	if($id_card) { $sql_kw .= " AND `id_card` like '%".trim($id_card)."%' "; }
	if($cname)    { $sql_kw .= " AND `cname` like '%".trim($cname)."%' "; }
	if($room_num) { $sql_kw .= " AND r.name LIKE '%".trim($room_num)."%' "; }
	// if($dong)     { $sql_kw .= " AND r.dong = '{$dong}' "; }
	// if($floor)    { $sql_kw .= " AND r.floor = '{$floor}' "; }
		
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

		$sql = "SELECT res.start_date, '{$def_in_use}' as 'end_date', res.power_status ,res.rate, 
		 '{$def_in_use}' as 'end_amount', '' as start_balance, '' as end_balance,
		m.username, m.cname, '{$def_in_use}' as `balance`, r.dong, r.floor, r.name ,m.id_card ,
		sec_to_time(TIMESTAMPDIFF(SECOND,res.start_date, now())) AS usetime
		FROM `room_study_situation` res
		LEFT JOIN `member` m ON m.id = res.member_id 
		INNER JOIN `room` r ON r.id = res.room_id
		WHERE res.power_status = 1   {$sql_kw}{$sql_dt} ORDER BY start_date DESC"; // 使用中				
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
		
		$sql = "SELECT rer.start_date, rer.end_date, '0' as 'power_status',  m.username, m.cname, 
				rer.rate AS balance, r.dong, r.floor, r.name, m.berth_number ,m.id_card, 
				 sec_to_time(TIMESTAMPDIFF(SECOND,rer.start_date, rer.end_date)) AS usetime,
				 hour(sec_to_time(TIMESTAMPDIFF(SECOND,rer.start_date, rer.end_date))) * d.rate AS use_price
				 , d.rate
				FROM `room_study_record` rer
				LEFT JOIN `member` m ON m.id = rer.member_id 
				LEFT JOIN `room` r ON r.id = rer.room_id
			    LEFT JOIN room_study_situation d ON  rer.room_id=d.room_id	
				WHERE 1 AND r.Title = '研習室' {$sql_kw}{$sql_dt} ORDER BY start_date DESC";				
		$rs  = $PDOLink->prepare($sql);
		$rs->execute();
		$data2 = $rs->fetchAll();
		$rs = null;
	}
	
	if(count($data1) > $max_col || count($data2) > $max_col )
	{
		$data1 = null;
		$data2 = null;
		die(header('Location: ../study-record.php?error=3'));		
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
	$data0 = null;
	$total_data = count($data);
	if($total_data > $max_col) 	
	{
		$data = null;
		die(header('Location: ../study-record.php?error=3'));	
	}  

	if($data) {
		
		$filename  = "研習室_電力使用紀錄匯出.csv";
		$body_date = "列印日期:".date("Ymd");
		$body_head = array("編號", "開始時間", "結束時間", "使用時間", "每小時收費", "房號", "卡號/姓名", "電費金額");
		$body_tail = array("經手人", "", "主辦出納", "", "主辦會計", "", "機關長官", "");
		
		$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$xls = $spreadsheet->getActiveSheet();
		
		foreach($data as $k => $row) {
			
			$s_time    = date($date_format.' '.$time_format, strtotime($row['start_date']));
			$e_time    = $row['end_date'] == $def_in_use ? $def_in_use : date($date_format.' '.$time_format, strtotime($row['end_date']));
			
 
			$showname  = $row['id_card'];
			$showname .= " / ";
			$showname .= $row['cname'];
 
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
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row["start_date"]);
			$xls->setCellValueByColumnAndRow($i++, $k+3,  $row["end_date"]);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row["usetime"]);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row["rate"]);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $row["name"]);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $showname);
			$xls->setCellValueByColumnAndRow($i++, $k+3,  $row['use_price']);
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
		header('Location: ../study-record.php?error=1');
	}
?>