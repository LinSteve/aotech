<?php
	require_once('../config/db.php');
	
	require '../vendor/autoload.php';
	
	include('../chk_log_in.php');
	
	set_time_limit(0);
	
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'enable'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$enable_arr = $rs->fetchAll();
	$enable_map = array();
	
	foreach($enable_arr as $v) {
		$enable_map[$v['custom_id']] = $v['custom_var'];
	}
	
	$sql  = "SELECT * FROM `group` ORDER BY `id`";	 
	$rs   = $PDOLink->query($sql);
	$data = $rs->fetchAll();
	
	if($data) {
		
		$filename  = "群組代碼表.csv";
		$body_head = array("群組代碼", "群組名稱", "用途說明", "備註", "狀態");
		
		$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$xls = $spreadsheet->getActiveSheet();
		
		foreach($data as $k => $row) {
			
			if($k == 0) {
				
				foreach($body_head as $i => $v) {				
					$xls->setCellValueByColumnAndRow($i+1, $k+1, $v);
				}
			}
						
			$i = 1;
			
			$xls->setCellValueByColumnAndRow($i++, $k+2, $row['id']);
			$xls->setCellValueByColumnAndRow($i++, $k+2, $row['name']);
			$xls->setCellValueByColumnAndRow($i++, $k+2, $row['usage']);
			$xls->setCellValueByColumnAndRow($i++, $k+2, $row['remark']);
			$xls->setCellValueByColumnAndRow($i++, $k+2, $enable_map[$row['enable']]);
		}
		
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Csv');
		$writer->setUseBOM(true);
		$writer->save('php://output');
		
	} else {
		header('Location: ../new-list.php?error=5');
	}
?>