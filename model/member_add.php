<?php
	require_once('../config/db.php');
	
	include('chk_log_in.php');

	$default_password = DEF_PWD;
	
	$room_max     = 16;
	$admin_group  = ADMIN_LIMIT;
	$admin        = $_SESSION['admin_user']['id'];
	$nowtime      = date('Y-m-d H:i:s');

	$member_name  = trim($_GET['member_name']);
	$room_num     = trim($_GET['room_strings']);
	$berth_number = trim($_GET['berth_number']);
	$member_sex   = $_GET['member_sex'];
	$identity     = $_GET['identity'];
 
	$member_pwd   = $default_password;
	$access_pwd   = $default_password;
	
	$balance      = '0';
	$lockmode     = '0';
	$accesslock   = '0'; // 無門禁 先給預設值
	$del_mark     = '0';

	$username     = '';
	$card_num     = '';
	$room_id      = '0';
	$member_grp   = array();
	
	$check_id     = '';
	$check_card   = '';
	$check_member = '';
	$check_room   = '';

	if(isset($_GET['username'])) 
	{
		$sql = "SELECT name FROM room WHERE Title='研習室'";
		$sp_room_data = func::excSQL($sql, $PDOLink, true);  // 研習室房號
		$sp_room_arr = array();
		foreach($sp_room_data  as $v){
		  $sp_room_arr[] = $v['name'];
		}	
		
		if($identity == '1') {
			
			// 一般管理員禁止新增管理員 -- 20200926
			$sql_chk  = "SELECT * FROM member WHERE id = '".$_SESSION['admin_user']['id']."'";
			$rs_chk   = $PDOLink->prepare($sql_chk); 
			$rs_chk->execute();
			$row_chk  = $rs_chk->fetch();
			$grp_data = json_decode($row_chk['group_id']);
			
			if($_SESSION['admin_user']['username'] == WEBADMIN || in_array('1', $grp_data)) {
				// pass
			} else {
				header('Location: ../new-member.php?error=9');
				return;
			}
			
			$username = $_GET['username'];
		} else {
			$username = str_pad(trim($_GET['username']),9,"0",STR_PAD_LEFT);
		}
	} else {
		header('Location: ../new-member.php?error=1');
		return;
	}
	
	if(isset($_GET['id_card'])) {
		$card_num  = str_pad(trim($_GET['id_card']),10,"0",STR_PAD_LEFT);
	} else {
		$card_num  = str_pad("0",10,"0",STR_PAD_LEFT);
	}
	if($card_num > 4294967295) die(header('Location: ../new-member.php?error=10'));
	
	if(isset($_GET['member_grp'])) {
		$member_grp = $_GET['member_grp'];
	}
	
	// 管理員群組上限
	if(in_array('1', $member_grp)) {
		
		$sql = "SELECT count(*) as 'count' FROM `member` WHERE group_id REGEXP 1 ";
		$tmp = $PDOLink->prepare($sql);
		$tmp->execute();
		$rs  = $tmp->fetch();
		$check = $rs['count'];
		
		if($check + 1 > $admin_group) {
			header('Location: ../new-member.php?error=6');
			return;
		}
	}
	
	# 檢查研修卡房號
	if($identity == '4')
	{ 
		// if(!in_array($room_num , $sp_room_arr))  die(header("location: ../new-member.php?error=11"));
		$room_num  ='';
		$berth_number = '';
	}
	if(in_array($room_num , $sp_room_arr)) { // 房號為研習室
		// $identity = '4';  
		// $member_grp=array(); 
		die(header("location: ../new-member.php?error=11"));
	}
	// 房間人數 -- 20200518
	if($room_num != '') {
		
		$sql = "SELECT id FROM `room` WHERE `name` = '{$room_num}'";
		$tmp = $PDOLink->prepare($sql);
		$tmp->execute();
		$rs  = $tmp->fetch();
		$room_id = $rs['id'];
		
		if($room_id == '') {
			header("location: ../new-member.php?error=4");
			return;			
		}
		
		$sql = "SELECT count(*) as 'count' FROM `member` 
				WHERE `room_strings` = '{$room_num}' AND del_mark = 0";
		$tmp = $PDOLink->prepare($sql);
		$tmp->execute();
		$rs  = $tmp->fetch();
		$count = $rs['count'];
		
		if($count >= $room_max) {
			header("location: ../new-member.php?error=5");
			return;
		}
	}
	
	// 有相同紀錄
	$sql = "SELECT * FROM member WHERE username = '{$username}' AND id_card = '{$card_num}'";
	$tmp = $PDOLink->prepare($sql);
	$tmp->execute();
	$rs  = $tmp->fetch();
	
	$check_id   = $rs['id'];
	$check_room = $rs['room_strings'];
	
	if($check_id != '') {
		
		$mem_old_arr = json_decode($rs['group_id']);
		
		$sql = "UPDATE `member` SET `del_mark` = '0', `group_id` = '".json_encode($member_grp)."', 
				`cname` = '{$member_name}', `room_strings` = '{$room_num}', 
				`berth_number` = '{$berth_number}', `sex` = '{$member_sex}', 
				`identity` = '{$identity}', `accesslock` = '{$accesslock}' WHERE `id` = '{$check_id}'";
		$flag = $PDOLink->exec($sql);
		
		// system_setting
		$hw_cmd = array('op' => 'update', 'table' => 'member', 'id' => $check_id);
		insert_system_setting($hw_cmd);
		
		// 房間現況紀錄 -- 20200812
		if($room_num != $check_room) {
			$sql = "SELECT id FROM `room` WHERE `name` IN ('{$check_room}', '{$room_num}')";
		} else {
			$sql = "SELECT id FROM `room` WHERE `name` = '{$room_num}'";
		}
		
		$tmp = $PDOLink->prepare($sql);
		$tmp->execute();
		$id_tmp  = $tmp->fetchAll();
		
		// 房號異動 初始化 -- 20200812
		foreach($id_tmp as $v) {
			$hw_cmd = array('op' => 'Single_Initialize', 'table' => 'room', 'id' => $v['id']);
			insert_system_setting($hw_cmd);
		}
		
		// 群組變更初始化 弘光才有 -- 20200902
		// if($member_grp !== $mem_old_arr) {
			// $hw_cmd = array('op' => 'MemberGroup_Initialize', 'table' => 'member', 'id' => $check_id, 'group_id_old' => $mem_old_arr);
			// insert_system_setting($hw_cmd);
		// }
		
		// log_list
		$content = "卡號重複更新: [member] id:{$check_id}, 管理員:{$admin}";
		$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '', '{$nowtime}'); ";
		$PDOLink->exec($log_ins_q);
		
		header('Location: ../new-member.php?success=1');
		return;
	}

	// 重複檢查改寫 -- 202020821
	if($username != '') {
		
		$sql = "SELECT * FROM `member` WHERE `username` = '{$username}'";
		$tmp = $PDOLink->prepare($sql);
		$tmp->execute();
		$rs  = $tmp->fetch();
		
		$check_member = $rs['username'];
		
		if($check_member != '') { // 有紀錄
			header("location: ../new-member.php?error=3");
			return;
		}
	}
	
	// 卡號重複檢查 -- 202020821
	if($card_num != str_pad('0',10,'0',STR_PAD_LEFT)) { 
	
		$sql = "SELECT * FROM `member` WHERE `id_card` = '{$card_num}' ";
		$tmp = $PDOLink->prepare($sql);
		$tmp->execute();
		$rs  = $tmp->fetch();
		
		$check_card = $rs['id_card'];
		
		if($check_card != '') { // 有紀錄
			header("location: ../new-member.php?error=3");
			return;
		}
	}
 try{

	$sql_update = "
		INSERT INTO `member` (`id`, `username`, `cname`, `password`, `access_password`, `id_card`, `room_strings`, 
		`berth_number`, `sex`, `balance`, `group_id`, `add_date`, `update_date`, `del_mark`, `identity`, `accesslock`) 
		VALUES (NULL, '{$username}', '{$member_name}', CONCAT('*', UPPER(SHA1(UNHEX(SHA1('{$member_pwd}'))))),  '{$access_pwd}', '{$card_num}', '{$room_num}', '{$berth_number}',
		'{$member_sex}', '{$balance}', '".json_encode($member_grp)."', '{$nowtime}', '{$nowtime}', '{$del_mark}', '{$identity}', '{$accesslock}')";
		
	$flag   = $PDOLink->exec($sql_update);
	$get_id = $PDOLink->lastInsertId();
	
	if($flag !== false) {			
			
		$hw_cmd = array('op' => 'insert', 'table' => 'member', 'id' => $get_id);
		insert_system_setting($hw_cmd);
		
		// room_electric_situation check
		$sql = "SELECT member_id FROM `room_electric_situation` WHERE member_id = '{$get_id}'";
		$tmp = $PDOLink->prepare($sql);
		$tmp->execute();
		$rs  = $tmp->fetch();
		$chk = $rs['member_id'];
		
		if($chk == '') {
			
			$elec_room_id = $room_id == '' ? '0' : $room_id;
			$sql = "INSERT INTO `room_electric_situation` (`id`, `member_id`, `room_id`, `powerstaus`, `start_amonut`, 
					`now_amount`, `start_balance`, `now_balance`, `start_date`, `update_date`) VALUES 
					(NULL, '{$get_id}', '{$elec_room_id}', '0', '0', '0', '0', '0', '{$nowtime}', '{$nowtime}')";
			$PDOLink->exec($sql);

			$new_id = $PDOLink->lastInsertId();
			
			$hw_cmd = array('op' => 'insert', 'table' => 'room_electric_situation', 'id' => $new_id);
			insert_system_setting($hw_cmd);
		}
		
		// 房間初始化 -- 20200805
		if($room_id != '') {
			$hw_cmd = array('op' => 'Single_Initialize', 'table' => 'room', 'id' => $room_id);
			insert_system_setting($hw_cmd);			
		}
		
		// log_list
		$content = " 新增 member ::id:{$get_id}::學號(員編):{$username}::卡號:{$card_num}::姓名:{$member_name}::管理員:{$admin} ";
		$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', 'new', '{$nowtime}'); ";
		$PDOLink->exec($log_ins_q);
		
		header('Location: ../new-member.php?success=1');
	} else {
		header('Location: ../new-member.php?error=1');
	}
}catch(PDOException $ex)
		{
			print $ex->getMessage();
			exit();
		} 
?>