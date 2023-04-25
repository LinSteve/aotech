<?php

require_once('../config/db.php');

require '../vendor/autoload.php';

include('../chk_log_in.php');

set_time_limit(0);

$data      = array();
$rs_data3  = array();

$sql_kw    = "";
$stored    = "Stored";
$refund    = "Refund";

$sel_year_all  = $_GET['sel_year_all'];

if($sel_year_all != '') {
	$sql_kw = " AND YEAR(e.add_date) = '{$sel_year_all}' ";
}										

$sql = "SELECT YEAR(e.add_date) as 'year', MONTH(e.add_date) as 'month', 
		SUM(e.PayValue) as 'amount', e.Sort 
		FROM `ezcard_record` e
		INNER JOIN room r ON r.id = e.room_id
		WHERE 1  AND r.Title = '研習室' {$sql_kw} GROUP BY YEAR(e.add_date), 
		MONTH(e.add_date), e.Sort ORDER BY e.add_date";
$rs  = $PDOLink->Query($sql);
$rs_tmp = $rs->fetchAll();

foreach($rs_tmp as $v) {
	
	$yy  = $v['year'];
	$mm  = $v['month'];
	$pos = $v['Sort'];
	$amt = $v['amount'];
	$shw = str_pad($yy,2,"0",STR_PAD_LEFT)."/".str_pad($mm,2,"0",STR_PAD_LEFT);
	
	$rs_data3[$yy][$mm]['day'] = $shw;
	
	if($pos == $stored) {
		$rs_data3[$yy][$mm]['amt'] += $amt;
	}
	
	if($pos == $refund) {
		$rs_data3[$yy][$mm]['ref'] += $amt;
	}
}

foreach($rs_data3 as $y => $row)
{
	foreach($row as $m => $v) 
	{
		$data[] = $v;
	}
}

if($data) {

	$filename  = "研習室_年份查詢報表.csv";
	$body_date = "列印日期:".date("Ymd");
	$body_head = array("日期", "電費總金額");
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
		// $xls->setCellValueByColumnAndRow($i++, $k+3, $row['amt']);
		// $xls->setCellValueByColumnAndRow($i++, $k+3, $row['ref']);
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
	header('Location: ../study-report.php?error=1');
}
?>