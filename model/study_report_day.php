<?php 
require_once('../config/db.php'); 
require '../vendor/autoload.php'; 
include('../chk_log_in.php');

set_time_limit(0);

$data      = array();
$rs_data1  = array();

$sql_kw    = "";

$stored    = "Stored";
$refund    = "Refund";

$date_format = 'Y/m/d';
$time_format = 'H:i:s';

$kw_start  = $_GET['kw_start'];
$kw_end    = $_GET['kw_end'];

if($kw_start) {
	$s_date = date('Y-m-d H:i:s', strtotime($kw_start));
	$sql_kw.= " AND e.add_date >= '{$s_date}' ";
}

if($kw_end) {
	$e_date = date('Y-m-d H:i:s', strtotime($kw_end.'+1 day'));
	$sql_kw.= " AND e.add_date < '{$e_date}' ";
}											

$sql = "SELECT e.*, m.cname, e.add_date as 'day' 
		FROM `ezcard_record` e 
		LEFT JOIN `member` m ON m.id = e.member_id 
		INNER JOIN room r ON r.id = e.room_id
		WHERE 1 AND r.Title = '研習室' {$sql_kw} ORDER BY e.add_date DESC";
$rs  = $PDOLink->Query($sql);
$rs_tmp = $rs->fetchAll();

foreach($rs_tmp as $v) {
	
	$day = $v['day'];
	$pos = $v['Sort'];
	$amt = $v['PayValue'];
	
	$nmn = $v['CardID'];
	$nmn.= " / ";
	$nmn.= $v['cname'];
	
	$rs_data1[$day]['day'] = $day;
	$rs_data1[$day]['nmn'] = $nmn;

	if($pos == $stored) {
		$rs_data1[$day]['amt'] += $amt;
	}
	
	if($pos == $refund) {
		$rs_data1[$day]['ref'] += $amt;
	}
}

foreach($rs_data1 as $v) {
	$data[] = $v;
}

if($data) 
{
	$filename  = "研習室_日期查詢報表.csv";
	$body_date = "列印日期:".date("Ymd");
	$body_head = array("日期", "卡號/姓名", "電費金額");
	$body_tail = array("經手人", "", "主辦出納", "", "主辦會計", "", "機關長官", "");
	
	$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
	$xls = $spreadsheet->getActiveSheet();
	
	foreach($data as $k => $row) 
	{
		
		$amt = $row['amt'] == '' ? 0 : $row['amt'];
		$ref = $row['ref'] == '' ? 0 : $row['ref'];
		$sum = $amt - $ref;
		$nmn = $row['nmn'];
		
		if($k == 0) {
			
			$xls->setCellValueByColumnAndRow(1, $k+1, $body_date);
			
			foreach($body_head as $i => $v) {				
				$xls->setCellValueByColumnAndRow($i+1, $k+2, $v);
			}
		}
					
		$i = 1;
		
		$xls->setCellValueByColumnAndRow($i++, $k+3, $row['day']);
		$xls->setCellValueByColumnAndRow($i++, $k+3, $nmn);
		// $xls->setCellValueByColumnAndRow($i++, $k+3, $amt);
		// $xls->setCellValueByColumnAndRow($i++, $k+3, $ref);
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