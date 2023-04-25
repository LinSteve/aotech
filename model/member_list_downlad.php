<?php
	require_once('../config/db.php');
	
	require '../vendor/autoload.php';
	
	include('../chk_log_in.php');
	
	set_time_limit(0);
	
	$sql_kw = " AND identity IN (0, 1) AND del_mark = '0' AND username != '".WEBADMIN."' ";
	$sql    = "SELECT * FROM `member` WHERE 1 {$sql_kw} ORDER BY `id` ";
	$rs     = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
		
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'sex'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$sex_arr = $rs->fetchAll();
	$sex_map = array();
	
	foreach($sex_arr as $v) {
		$sex_map[$v['custom_id']] = $v['custom_var'];
	}
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'login'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$login_arr = $rs->fetchAll();
	$login_map = array();
	
	foreach($login_arr as $v) {
		$login_map[$v['custom_id']] = $v['custom_var'];
	}
	
	if($data) {
		
		$filename  = "現有名單匯出.csv";
		$body_head = array("學號/教職員工編號", "姓名", "性別", "卡號", "房號", "群組", "登入身分");
		
		$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$xls = $spreadsheet->getActiveSheet();
		
		foreach($data as $k => $row) {
			
			if($k == 0) {
				
				foreach($body_head as $i => $v) {				
					$xls->setCellValueByColumnAndRow($i+1, $k+1, $v);
				}
			}
						
			$i = 1;
			
			$group_tmp = json_decode($row['group_id']);
			$group_id  = implode(',', $group_tmp);
			
			$xls->setCellValueByColumnAndRow($i++, $k+2, $row['username']);
			$xls->setCellValueByColumnAndRow($i++, $k+2, $row['cname']);
			$xls->setCellValueByColumnAndRow($i++, $k+2, $sex_map[$row['sex']]);
			$xls->setCellValueByColumnAndRow($i++, $k+2, $row['id_card']);
			$xls->setCellValueByColumnAndRow($i++, $k+2, $row['room_strings']);
			$xls->setCellValueByColumnAndRow($i++, $k+2, $group_id);
			$xls->setCellValueByColumnAndRow($i++, $k+2, $login_map[$row['identity']]);
			// $xls->setCellValueByColumnAndRow($i++, $k+2, $row['enable']);
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