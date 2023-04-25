<?php
	include_once("../config/db.php");
	include('../chk_log_in.php');
	require '../vendor/autoload.php';

	$room_max     = 16;
	$admin_group  = ADMIN_LIMIT;
	$room_chkout  = '';
	$default_pwd  = DEF_PWD;
	$room_num_arr = array();
	$room_current = array();
	$admin        = $_SESSION['admin_user']['id'];

	$Import_TmpFile = $_FILES['link1']['tmp_name'];
	$Import_NameFile = $_FILES['link1']['name'];  
	$file = $Import_TmpFile;
  
    try {
		
		// by default assumes that the loaded CSV file is UTF-8 encoded. If you are reading CSV files 			
		// that were created in Microsoft Office Excel the correct input encoding may rather be Windows-1252 (CP1252)
		// $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
		// $reader->setInputEncoding('ANSI');
		// $reader->setDelimiter(';');
		// $reader->setEnclosure('');
		// $reader->setSheetIndex(0);
		
		// $spreadsheet = $reader->load($file);
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

    } catch(Exception $e) {   
        
        die('Error loading file "'.pathinfo($file,PATHINFO_BASENAME).'": '.$e->getMessage());
		
		// header("location: ../new-list.php?error=1");
		
		return;
    }
	
    // 匯入欄位順序：學號 / 員編, 姓名, 性別, 卡號, 房號, 群組, 身分組
    // $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);   // $key => $col
	$sheetData = $spreadsheet->getSheet(0)->toArray();
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'sex'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$sex_arr = $rs->fetchAll();
	$sex_rev = array();
	
	foreach($sex_arr as $v) {
		$sex_rev[$v['custom_var']] = $v['custom_id'];
	}
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'login'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$login_arr = $rs->fetchAll();
	$login_rev = array();
	
	foreach($login_arr as $v) {
		$login_rev[$v['custom_var']] = $v['custom_id'];
	}
	
	$sql = "SELECT name FROM room GROUP BY name";
	$tmp = $PDOLink->prepare($sql);
	$tmp->execute();
	$rs  = $tmp->fetchAll();
	
	foreach($rs as $v) {
		$room_current[] = $v['name'];
	}
	$room_current[] = $room_chkout;
	
	// 改寫 -- 20200122
	foreach($sheetData as $col) 
	{
		// 找符合格式的 -- 20200212
		if(preg_match("/^([0-9A-Za-z]+)$/", $col[0]))
		{
			// 登入身分
			// $identit_tmp = trim($col[6]);
			$identit_tmp = (trim($col[6]) == '') ? '學生' : trim($col[6]);  // 無輸入預設值學生  
			$identity = $login_rev[$identit_tmp];
			if($identity == '4') die(header('Location: ../new-list.php?error=11')); // 研習卡不可匯入
			
			if($identity != '1') {
				$username = str_pad(trim($col[0]),9,"0",STR_PAD_LEFT);
			} else {
				$username = trim($col[0]); // 管理員不補0 -- 20200813
			}
			$card_num = str_pad(trim($col[3]),10,"0",STR_PAD_LEFT);
			if($card_num > 4294967295) die(header('Location: ../new-list.php?error=10'));
			
			$room       = trim($col[4]);
			$member_grp = array();
			$member_tmp = trim($col[5]);

			if($member_tmp != '') {
				$member_grp = explode(',', $member_tmp);
			}			

			if(($identit_tmp=='學生' || $identit_tmp=='公用卡') && $member_tmp!='') die(header('Location: ../new-list.php?error=12')); # 匯入身份為學生,設權限為管理員的防呆處理			
			else if(in_array('1', $member_grp)) die(header('Location: ../new-list.php?error=8')); # 不允許匯入超級管理員

			if($room != '') {
				
				// 非系統房號 -- 20200210
				if(!in_array($room, $room_current) || in_array($room, array('B02', 'B03','B04'))) {
					header("location: ../new-list.php?error=4&room=".$room);
					return;
				}
				
				$sql = "SELECT id FROM member WHERE username = '{$username}' AND room_strings = '{$room}'";
				$rs = $PDOLink->prepare($sql);  
				$rs->execute();
				$userRow = $rs->fetch();  
				$chkid   = $userRow['id'];
				
				if($chkid == '') {
					$room_num_arr[$room] += 1;
				} else {
					$room_num_arr[$room] -= 1; // 扣掉 匯入時計算
				}
			}
			
			// 管理員群組上限
			if(in_array('3', $member_grp)) {
				
				$sql = "SELECT id FROM `member` WHERE username = '{$username}' AND del_mark = '0' AND group_id REGEXP '3' ";
				$rs = $PDOLink->prepare($sql);  
				$rs->execute();
				$userRow = $rs->fetch();  
				$chkgid   = $userRow['id'];
				
				if($chkgid == '') {
					
					$sql = "SELECT count(*) as 'count' FROM `member` WHERE del_mark = '0' AND group_id REGEXP '3' ";
					$tmp = $PDOLink->prepare($sql);
					$tmp->execute();
					$rs  = $tmp->fetch();
					$check = $rs['count'];
					
					if($check + 1 > $admin_group) {
						header('Location: ../new-list.php?error=6');
						return;
					}
				}
			}
		}
	}

	foreach($room_num_arr as $k => $v) 
	{	
		$RoomTotalSQL = "SELECT count(*) FROM member WHERE room_strings = '{$k}' ";
		$RoomTotalRs  = $PDOLink->query($RoomTotalSQL);
		$RoomTotalRowNum = $RoomTotalRs->fetchcolumn();
		$AddRoomTotalRowNum = $RoomTotalRowNum + $v;

		// 退宿匯入更新 -- 20200210
		if($k == $room_chkout) {
			
			// 
			
		} else if($AddRoomTotalRowNum <= $room_max) {	
			
			// 
			
		} else {
			header("location: ../new-list.php?error=1");
			return;
		}
	}
	
    foreach($sheetData as $col) 
	{		
		$nowtime      = date('Y-m-d H:i:s');
		$sql_update   = '';
		$check_member = '';
		$check_card   = '';
		
        // 找符合格式的
		if(preg_match("/^([0-9A-Za-z]+)$/", $col[0])) 
		{
			// 登入身分
			// $identit_tmp = trim($col[6]);
			$identit_tmp = (trim($col[6]) == '') ? '學生' : trim($col[6]);  // 無輸入預設值學生 
			$identity = $login_rev[$identit_tmp];
			
			// 學號、員編 
			if($identity != '1') {
				$username = str_pad(trim($col[0]),9,"0",STR_PAD_LEFT);
			} else {
				$username = trim($col[0]); // 管理員不補0 -- 20200813
				
				// 一般管理員禁止新增管理員 -- 20200926
				$sql_chk  = "SELECT * FROM member WHERE id = '".$_SESSION['admin_user']['id']."'";
				$rs_chk   = $PDOLink->prepare($sql_chk); 
				$rs_chk->execute();
				$row_chk  = $rs_chk->fetch();
				$grp_data = json_decode($row_chk['group_id']);
				
				if($_SESSION['admin_user']['username'] == WEBADMIN || in_array('3', $grp_data)) {
					// pass
				} else {
					header('Location: ../new-list.php?error=9');
					return;
				}
			}

			// 姓名
			$member_name  = func::removeUtf8Char4bytes(trim($col[1]));
			//$test_name  = base64_encode(trim($col[1]));
			// 性別
			$csv_sex      = trim($col[2]);
			$member_sex   = $sex_rev[$csv_sex];

			// 密碼
			$member_pwd   = $default_pwd;

			// 門禁密碼
			$access_pwd   = $default_pwd;

			// 卡號
			$card_num     = str_pad(trim($col[3]),10,"0",STR_PAD_LEFT);

			// 房號
			$room_num     = strtoupper(trim($col[4]));
			
			// 床號
			$berth_number = trim($col[7]);
			
			// 群組
			$member_grp   = array();
			$member_tmp   = trim($col[5]);
			
			if($member_tmp != '') {
				$member_grp = explode(',', $member_tmp);
			}
			
			$balance      = '0'; // 預設金額
			$lockmode     = '0';
			$del_mark     = '0';
			$accesslock   = '0';
			
			$check_id     = '';
			$check_card   = '';
			$check_member = '';
			$check_room   = '';

			$sql = "SELECT * FROM member WHERE username = '{$username}' AND id_card = '{$card_num}'";
			$tmp = $PDOLink->prepare($sql);
			$tmp->execute();
			$rs  = $tmp->fetch();
			
			$check_id   = $rs['id'];
			$check_room = $rs['room_strings'];
			
			if($check_id != '') {
				
				$mem_old_arr = json_decode($rs['group_id']);
				
				$sql = "SET sql_mode = NO_BACKSLASH_ESCAPES;UPDATE `member` SET `del_mark` = '0', `group_id` = '".json_encode($member_grp)."', 
						`cname` = '{$member_name}', `room_strings` = '{$room_num}', `berth_number` = '{$berth_number}', `sex` = '{$member_sex}', 
						`identity` = '{$identity}', `accesslock` = '{$accesslock}',  
						`password` = CONCAT('*', UPPER(SHA1(UNHEX(SHA1({$member_pwd}))))), update_date = '{$nowtime}' WHERE `id` = '{$check_id}'";
						
									
				$flag = $PDOLink->exec($sql);
				
				// system_setting
				$hw_cmd = array('op' => 'update', 'table' => 'member', 'id' => $check_id);
				insert_system_setting($hw_cmd);
				
				$sql = "SELECT id FROM `room` WHERE `name` = ?";
				$stmt = $PDOLink->prepare($sql);
				$stmt->execute(array($room_num));
				$room_id_data  = $stmt->fetch();  
				$room_id_ = ($room_id_data) ? $room_id_data['id']: '0'; // 空值記零
				if($room_id_ != '')
				{
					$sql = "UPDATE room_electric_situation SET room_id= ? WHERE member_id = ? "; // 現況房號資料更新
					$stmt = $PDOLink->prepare($sql);
					$updated = $stmt->execute(array($room_id_ ,$check_id));
					if($updated)
					{ 
						$stmt = $PDOLink->query("SELECT id FROM  room_electric_situation WHERE member_id = {$check_id} LIMIT 1 "); 
						$rows = $stmt->fetch();
						
						$hw_cmd = array('op' => 'update', 'table' => 'room_electric_situation', 'id' => $rows['id'] );
						insert_system_setting($hw_cmd);
					}				
				} 		
				
				// 房間現況紀錄 -- 20200812
				if($room_num != $check_room) {
					$sql = "SELECT id FROM `room` WHERE `name` IN ('{$check_room}', '{$room_num}')";
				} else {
					$sql = "SELECT id FROM `room` WHERE `name` = '{$room_num}'";
				}
				
				$tmp = $PDOLink->prepare($sql);
				$tmp->execute();
				$id_tmp  = $tmp->fetchAll();
				
				// 房號異動 初始化 -- 20200812 --> 2022-07-18 改最後進行全部初始化即可
				/*
				foreach($id_tmp as $v) {
					$hw_cmd = array('op' => 'Single_Initialize', 'table' => 'room', 'id' => $v['id']);
					insert_system_setting($hw_cmd);
				}
				*/
				// 群組變更初始化 弘光才有-- 20200902
				// if($member_grp !== $mem_old_arr) {
					// $hw_cmd = array('op' => 'MemberGroup_Initialize', 'table' => 'member', 'id' => $check_id, 'group_id_old' => $mem_old_arr);
					// insert_system_setting($hw_cmd);
				// }
				
				// log_list
				$content = "卡號重複更新: [member] id:{$check_id}, 管理員:{$admin}";
				$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '', '{$nowtime}'); ";
				$PDOLink->exec($log_ins_q);
				
				// header('Location: ../new-member.php?success=1');
				continue;
			}
			
			$sql = "SELECT id FROM `member` WHERE username = '{$username}'";
			$tmp = $PDOLink->prepare($sql);
			$tmp->execute();
			$rs  = $tmp->fetch();
			
			$check_member = $rs['id'];
			
			// 學號 員編
			if($check_member != '') {
				
				$sql_update = "SET sql_mode = NO_BACKSLASH_ESCAPES;
						UPDATE `member` SET `cname` = '{$member_name}',
						`id_card` = '{$card_num}', `room_strings` = '{$room_num}',
						`sex` = '{$member_sex}', `group_id` = '".json_encode($member_grp)."',
						`berth_number` = '{$berth_number}', `del_mark` = '{$del_mark}',
						`identity` = '{$identity}', `lockmode` = '{$lockmode}', 
						update_date = '{$nowtime}' WHERE id = '{$check_member}'";
				$PDOLink->exec($sql_update);
				
				// system_setting
				$hw_cmd = array('op' => 'update', 'table' => 'member', 'id' => $check_member);
				insert_system_setting($hw_cmd);

				// check room_electric_situation
				$sql = "SELECT member_id FROM `room_electric_situation` WHERE member_id = '{$check_member}'";
				$tmp = $PDOLink->prepare($sql);
				$tmp->execute();
				$rs  = $tmp->fetch();
				$chk = $rs['member_id'];
				
				$room_id = 0;
				
				if($chk == '') {
					
					$sql = "SELECT id FROM `room` WHERE `name` = '{$room_num}'";
					$tmp = $PDOLink->prepare($sql);
					$tmp->execute();
					$rs  = $tmp->fetch();
					
					if($rs) { $room_id = $rs['id']; }
					
					$sql = "INSERT INTO `room_electric_situation` (`member_id`, `room_id`, `powerstaus`, `start_amonut`, 
							`now_amount`, `start_balance`, `now_balance`, `start_date`, `update_date`) VALUES 
							('{$check_member}', '{$room_id}', '0', '0', '0', '0', '0', '{$nowtime}', '{$nowtime}')";
					$PDOLink->exec($sql);
					$new_id = $PDOLink->lastInsertId();
					
					$hw_cmd = array('op' => 'insert', 'table' => 'room_electric_situation', 'id' => $new_id);
					insert_system_setting($hw_cmd);
				}
				
				// log_list
				$content = " 更新::id:{$check_member}::學號(員編):{$username}::卡號:{$card_num}::姓名:{$member_name}::管理員:{$admin} ";
				$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '{$op}', '{$nowtime}'); ";
				$PDOLink->exec($log_ins_q);
				
			} else { // 新增
				if($identity != '3' && $card_num != str_pad('0',10,'0',STR_PAD_LEFT)) { // 排除 (1)身份為:維修卡 (2)卡號全部為0的
					$sql = "SELECT * FROM member WHERE id_card='".trim($card_num)."'"; // 卡號重複檢查
					$card_data = func::excSQL($sql, $PDOLink, false);
					if($card_data) die(header("location: ../new-list.php?error=7"));
				}
				
	
				$sql_update = "SET NAMES 'utf8';
					INSERT INTO `member` (`username`, `cname`, `password`, `access_password`, `id_card`, `room_strings`, `berth_number`,
					`sex`, `balance`, `group_id`, `add_date`, `update_date`, `del_mark`, `identity`, `lockmode`, `accesslock` ) 
					VALUES ('{$username}', '{$member_name}', CONCAT('*', UPPER(SHA1(UNHEX(SHA1({$member_pwd}))))), '{$access_pwd}', '{$card_num}', '{$room_num}', '{$berth_number}',
					'{$member_sex}', '{$balance}', '".json_encode($member_grp)."', '{$nowtime}', '{$nowtime}', '{$del_mark}', '{$identity}', '{$lockmode}', '{$accesslock}')";
					
				$PDOLink->exec($sql_update);
				
				$get_id = $PDOLink->lastInsertId(); 
				
				// -- 20200417				
				$hw_cmd = array('op' => 'insert', 'table' => 'member', 'id' => $get_id);
				insert_system_setting($hw_cmd);
				
				// -- 20200428
				$sql = "SELECT member_id FROM `room_electric_situation` WHERE member_id = '{$get_id}'";
				$tmp = $PDOLink->prepare($sql);
				$tmp->execute();
				$rs  = $tmp->fetch();
				$chk = $rs['member_id'];
				
				$room_id = 0;
				
				if($chk == '') {
					
					$sql = "SELECT id FROM `room` WHERE `name` = '{$room_num}'";
					$tmp = $PDOLink->prepare($sql);
					$tmp->execute();
					$rs  = $tmp->fetch();
					
					if($rs) { $room_id = $rs['id']; }
					
					$sql = "INSERT INTO `room_electric_situation` (`member_id`, `room_id`, `powerstaus`, `start_amonut`, 
							`now_amount`, `start_balance`, `now_balance`, `start_date`, `update_date`) VALUES 
							('{$get_id}', '{$room_id}', '0', '0', '0', '0', '0', '{$nowtime}', '{$nowtime}')";
					$PDOLink->exec($sql);
					$new_id = $PDOLink->lastInsertId();
					
					// -- 20200417
					$hw_cmd = array('op' => 'insert', 'table' => 'room_electric_situation', 'id' => $new_id);
					insert_system_setting($hw_cmd);
				}
				
				$content = " 新增::id:{$get_id}::學號(員編):{$username}::卡號:{$card_num}::姓名:{$member_name}::管理員:{$admin} ";
				$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', 'import', '{$nowtime}'); ";
				$PDOLink->exec($log_ins_q);
			}
			
		} else {
			
			// log_list
			$content = " 非匯入格式::{$col[0]}::管理員:{$admin} ";
			$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', 'import', '{$nowtime}'); ";
			$PDOLink->exec($log_ins_q);
		}
    }
	
	// 匯入確認 -- 20200518 
	foreach($sheetData as $col) 
	{
		// 找符合格式的
		if(preg_match("/^([0-9A-Za-z]+)$/", $col[0]))
		{
			// 登入身分
			// $identit_tmp = trim($col[6]);
		    $identit_tmp = (trim($col[6]) == '') ? '學生' : trim($col[6]);  // 無輸入預設值學生 
			$identity    = $login_rev[$identit_tmp];
			
			// 學號、員編 
			if($identity != '1') {
				$username = str_pad(trim($col[0]),9,"0",STR_PAD_LEFT);
			} else {
				$username = trim($col[0]); // 管理員不補0 -- 20200813
			}
			
			$user_q = "SELECT * FROM `member` WHERE `username` = '{$username}' ";
			$user_r = $PDOLink->prepare($user_q);  
			$user_r->execute();
			$userRow = $user_r->fetch();  
			$chkname = $userRow['username'];
			
			if($chkname == '') {
				header("location: ../new-list.php?error=8");
				return;
			}
		}
	}
	
    // 全部初始化
	$hw_cmd = array('op' => 'ALL_Initialize', 'table' => 'room');
	insert_system_setting($hw_cmd);
	
	$PDOLink = null;
  
	header("location: ../new-list.php?success=1");
?>