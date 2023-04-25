<?php
	require_once('../../config/db.php');
	
	require '../../vendor/autoload.php';
	
	// include('../chk_log_in.php');
	
	set_time_limit(0);
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';
	
	$sql = "SELECT * FROM system_setting 
			WHERE computer_name = 'Web' OR title = '工程模式' 
			ORDER BY add_date DESC";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	if($data) {
		
		$filename  = "sql_list.csv";
		$body_date = "列印日期:".date("Ymd");
		$body_head = array("編號", "建立日期", "修改訊息", "修改類別");
		$body_tail = array("經手人", "", "主辦出納", "", "主辦會計", "", "機關長官", "");
		
		$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$xls = $spreadsheet->getActiveSheet();
		
		foreach($data as $k => $row) {
			
			$showtime  = date($date_format.' '.$time_format, strtotime($row["add_date"]));
			$show_msg  = $row['c_code'];
			$show_cate = $row['title'].$row['computer_name'];
			
			if($k == 0) {
				
				$xls->setCellValueByColumnAndRow(1, $k+1, $body_date);
				
				foreach($body_head as $i => $v) {				
					$xls->setCellValueByColumnAndRow($i+1, $k+2, $v);
				}
			}
						
			$i = 1;
			
			$xls->setCellValueByColumnAndRow($i++, $k+3, $k+1);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $showtime);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $show_msg);
			$xls->setCellValueByColumnAndRow($i++, $k+3, $show_cate);
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
		header('Location: ../log-sql.php?error=1');
	}
?>