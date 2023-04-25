<?php

require_once('../config/db.php');

require '../vendor/autoload.php';

include('../chk_log_in.php');

set_time_limit(0);

$data      = array();
$rs_data2  = array();

$sql_kw    = "";
$stored    = "Stored";
$refund    = "Refund";

$date_format = 'Y/m/d';
$time_format = 'H:i:s';

$sel_year_start  = $_GET['sel_year_start'];
$sel_year_end    = $_GET['sel_year_end'];
$sel_month_start = $_GET['sel_month_start'];
$sel_month_end   = $_GET['sel_month_end'];

if($sel_year_start != '' & $sel_month_start != '') {
	
	$qry_date = date('Y-m-d H:i:s', strtotime("{$sel_year_start}-{$sel_month_start}-01 00:00:00"));
	
	$sql_kw .= " AND add_date >= '{$qry_date}' ";
	
} else {

	if($sel_year_start != '') {
		$sql_kw .= " AND YEAR(add_date) >= {$sel_year_start} ";
	} 
	
	if($sel_month_start != '') {
		$sql_kw .= " AND MONTH(add_date) >= {$sel_month_start} ";
	}												
}
	
if($sel_year_end != '' & $sel_month_end != '') {
	
	$qry_date = date('Y-m-d H:i:s', strtotime("{$sel_year_end}-{$sel_month_end}-01 00:00:00 +1 month"));
	
	$sql_kw .= " AND add_date < '{$qry_date}' ";
	
} else {
	if($sel_year_end != '') {
		$sql_kw .= " AND YEAR(add_date) <= {$sel_year_end} ";
	} 
	
	if($sel_month_end != '') {
		$sql_kw .= " AND MONTH(add_date) <= {$sel_month_end} ";
	}												
}

$sql = "SELECT YEAR(add_date) as 'year', MONTH(add_date) as 'month', 
		DAY(add_date) as 'day', SUM(PayValue) as 'amount', Sort 
		FROM `ezcard_record` WHERE 1 {$sql_kw} GROUP BY YEAR(add_date), 
		MONTH(add_date), DAY(add_date), Sort ORDER BY add_date";
$rs  = $PDOLink->Query($sql);
$rs_tmp = $rs->fetchAll();

foreach($rs_tmp as $v) {
	
	$yy  = $v['year'];
	$mm  = $v['month'];
	$dd  = $v['day'];
	$pos = $v['Sort'];
	$amt = $v['amount'];
	$shw = date($date_format, strtotime($yy.'-'.$mm.'-'.$dd));
	
	$rs_data2[$yy][$mm][$dd]['day'] = $shw;
	
	if($pos == $stored) {
		$rs_data2[$yy][$mm][$dd]['amt'] += $amt;
	}
	
	if($pos == $refund) {
		$rs_data2[$yy][$mm][$dd]['ref'] += $amt;
	}
}

foreach($rs_data2 as $y => $outer)
{
	foreach($outer as $m => $inner) 
	{
		foreach($inner as $d => $row) 
		{
			$data[] = $row;
		}
	}
}


if($data) {

	$filename  = "月份查詢報表.csv";
	$body_date = "列印日期:".date("Ymd");
	$body_head = array("日期", "儲值金額", "退費金額", "小計", "備註");
	$body_tail = array("經手人", "", "主辦出納", "", "主辦會計", "", "機關長官", "");
	
	$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
	$xls = $spreadsheet->getActiveSheet();
	
	foreach($data as $k => $row) 
	{
		
		$sum = $row['amt'] - $row['ref'];
		
		if($k == 0) {
			
			$xls->setCellValueByColumnAndRow(1, $k+1, $body_date);
			
			foreach($body_head as $i => $v) {				
				$xls->setCellValueByColumnAndRow($i+1, $k+2, $v);
			}
		}
					
		$i = 1;
		
		$xls->setCellValueByColumnAndRow($i++, $k+3, $row['day']);
		$xls->setCellValueByColumnAndRow($i++, $k+3, $row['amt']);
		$xls->setCellValueByColumnAndRow($i++, $k+3, $row['ref']);
		$xls->setCellValueByColumnAndRow($i++, $k+3, $sum);
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
	header('Location: ../power-report.php?error=1');
}
?>