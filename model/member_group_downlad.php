<?php
	require_once('../config/db.php');
	
	require '../vendor/autoload.php';
	
	include('../chk_log_in.php');
	
	set_time_limit(0);
	
	$sql_kw   = " AND del_mark = '0' AND username != '".WEBADMIN."' ";
	
 	$cname    = $_POST['cname'];
 	$username = $_POST['username'];
 	$room_num = $_POST['room_strings'];
	$resident = $_POST['resident'];
	
	$group_id = $_POST['member_grp'];
	
	if($username) { $sql_kw .= " AND `username` = '".trim($username)."' "; }
	if($cname)    { $sql_kw .= " AND `cname` = '".trim($cname)."' "; }
	if($room_num) { $sql_kw .= " AND  concat(room_strings, berth_number) LIKE '%".trim($room_num)."%' "; }
	
	if(isset($group_id)) {
		foreach($group_id as $v) {
			if($v != '') {
				$sql_kw .= " AND group_id REGEXP '[[:<:]]{$v}[[:>:]]' ";
			}
		}
	}
	
	$sql = "SELECT * FROM `member` WHERE 1 {$sql_kw} ";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	$sql = "SELECT * FROM `group`";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$group_arr = $rs->fetchAll();
	$group_map = array();
	
	foreach($group_arr as $v) {
		$group_map[$v['id']] = $v['name'];
	}
	
	$sql = 'SELECT * FROM `room`';
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$room_arr = $rs->fetchAll();
	$room_map = array();
	
	foreach($room_arr as $v) {
		$room_map[$v['name']] = $v['dong']." / ".$v['floor'];
	}
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'sex'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$sex_arr = $rs->fetchAll();
	$sex_map = array();
	
	foreach($sex_arr as $v) {
		$sex_map[$v['custom_id']] = $v['custom_var'];
	}
	
	if($data) {
		
		$filename  = "群組名單匯出.csv";
		$body_head = array("編號", "姓名", "性別", "所在棟別/可到樓層", "房號/床號", "備註", "所屬群組");
		
		$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$xls = $spreadsheet->getActiveSheet();
		
		foreach($data as $k => $row) {
			
			$group_arr = array();
			$group_tmp = json_decode($row['group_id']);
			
			foreach($group_tmp as $v) {
				$group_arr[] = $group_map[$v];
			}
			
			$group_str = implode(',', $group_arr);
			
			if($k == 0) {
				
				foreach($body_head as $i => $v) {				
					$xls->setCellValueByColumnAndRow($i+1, $k+1, $v);
				}
			}
						
			$i = 1;
			
			$xls->setCellValueByColumnAndRow($i++, $k+2, $row['username']);
			$xls->setCellValueByColumnAndRow($i++, $k+2, $row['cname']);
			$xls->setCellValueByColumnAndRow($i++, $k+2, $sex_map[$row['sex']]);
			$xls->setCellValueByColumnAndRow($i++, $k+2, $room_map[$row['room_strings']]);
			$xls->setCellValueByColumnAndRow($i++, $k+2, $row['room_strings'].'/'.$row['berth_number']);
			$xls->setCellValueByColumnAndRow($i++, $k+2, '');
			$xls->setCellValueByColumnAndRow($i++, $k+2, $group_str);
		}
		
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Csv');
		$writer->setUseBOM(true);
		$writer->save('php://output');
		
	} else {
		header('Location: ../new-groupsearch.php?error=1');
	}
?>