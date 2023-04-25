<?php 

include_once('../../config/db.php');

// include('../chk_log_in.php');

$room_max     = 16;
$admin_group  = ADMIN_LIMIT;
$admin        = $_SESSION['admin_user']['id'];
$nowtime      = date('Y-m-d H:i:s');

$id           = $_POST["id"];
$username_o   = $_POST["username"];
$cname        = $_POST["member_name"];
$member_sex   = $_POST["member_sex"];
$room_num     = $_POST['room_strings'];
$berth_number = $_POST['berth_number'];
$balance	  = $_POST['balance'];
$identity  = $_POST['identity'];
$card_num     = $_POST["id_card"];

$sql = "SELECT name FROM room WHERE Title = '研習室' ";
$sp_room_data = func::excSQL($sql, $PDOLink, true);  // 研習室房號
$sp_room_arr = array();
foreach($sp_room_data  as $v){
  $sp_room_arr[] = $v['name'];
}	

if($identity == '4')
{
	// if(!in_array($room_num , $sp_room_arr)) die(header("location: ../member_edit.php?id={$id}&error=8"));
	$room_num = '';
	$berth_number = '';	
	$member_grp = array();
}
if(in_array($room_num , $sp_room_arr) && $identity != '4' )  die(header("location: ../member_edit.php?id={$id}&error=8"));   

$id_card  = str_pad(trim($card_num),10,'0',STR_PAD_LEFT);
if($id_card > 4294967295) die(header("Location: ../member_edit.php?id={$id}&error=7"));

$username     = '';
if($identity != '1'  && $identity != '5') {
	$username = str_pad(trim($username_o),9,"0",STR_PAD_LEFT);
} else {
	$username = trim($username_o); // 管理員、公用卡不補0 -- 20220928
}

$id_card_old  = $_POST["id_card_old"];
$username_old = $_POST["username_old"];
$room_num_old = $_POST['room_strings_old'];
$identity_old = $_POST['identity_old'];

$mem_grp      = $_POST['member_grp'];
$mem_grp_old  = $_POST['member_grp_old'];

$accesslock   = '0'; // 無門禁 先給預設值
$del_mark     = '0';

$member_grp   = array();
$mem_old_arr  = array();

foreach($mem_grp as $v) {
	$member_grp[] = $v;
}

foreach($mem_grp_old as $v) {
	$mem_old_arr[] = $v;
}
 
// 管理員群組上限
if(in_array('3', $member_grp) & !in_array('3', $mem_grp_old)) {
	
	// $sql = "SELECT count(*) as 'count' FROM `member` WHERE del_mark = '0' AND group_id REGEXP '[[:<:]]1[[:>:]]'";
	$sql = "SELECT count(*) as 'count' FROM `member` WHERE del_mark = '0' AND group_id REGEXP '3'";
	$tmp = $PDOLink->prepare($sql);
	$tmp->execute();
	$rs  = $tmp->fetch();
	$check = $rs['count'];
	
	if($check + 1 > $admin_group) {
		header("location: ../member_edit.php?id={$id}&error=6");
		return;
	}
}

$sql = "SELECT * FROM `member` WHERE `username` = '{$username}' AND id != '{$id}' AND identity <> 3";
$tmp = $PDOLink->prepare($sql);
$tmp->execute();
$rs  = $tmp->fetch();

$check_user = $rs['username'];

if($check_user != '') {
	header("Location: ../member_edit.php?id={$id}&error=3");
	return;
}

if($id_card != '' && $id_card != str_pad('0',10,'0',STR_PAD_LEFT) && $identity == '0') {
	$sql = "SELECT * FROM `member` WHERE `id_card` = '{$id_card}' AND id != '{$id}' AND identity <> 3";
	$tmp = $PDOLink->prepare($sql);
	$tmp->execute();
	$rs  = $tmp->fetch();
	$check_card = $rs['id'];
	
	if($check_card != '') {
		header("Location: ../member_edit.php?id={$id}&error=2");
		return;
	}
}

if($room_num != '') {
	$sql = "SELECT id FROM `room` WHERE `name` = '{$room_num}'";
	$tmp = $PDOLink->prepare($sql);
	$tmp->execute();
	$rs  = $tmp->fetch();
	$room_id = $rs['id'];

	// check room -- 20200428
	if($room_id == '') {
		header("location: ../member_edit.php?id={$id}&error=4");
		return;
	}
	
	// 房間人數 -- 20200814
	$sql = "SELECT count(*) as 'count' FROM `member` 
			WHERE `room_strings` = '{$room_num}' AND del_mark = 0";
	$tmp = $PDOLink->prepare($sql);
	$tmp->execute();
	$rs  = $tmp->fetch();
	$count = $rs['count'];
	
	if($count >= $room_max) {
		header("location: ../member_edit.php?id={$id}&error=5");
		return;
	}
}
 
$sql  = "UPDATE `member` SET `del_mark` = '{$del_mark}', `username` = '{$username}', `cname` = '{$cname}', 
		`id_card` = '{$id_card}', `room_strings` = '{$room_num}', `berth_number` = '{$berth_number}',
		`sex` = '{$member_sex}', `group_id` = '".json_encode($member_grp)."', 
		`balance` = '{$balance}', `identity` = '{$identity}',
		`update_date` = '{$nowtime}' WHERE `id` = {$id}"; 
$updated = func::excSQLwithParam('update', $sql, array(), false, $PDOLink);

if($updated) {

	// system_setting
	$hw_cmd = array('op' => 'update', 'table' => 'member', 'id' => $id);
	insert_system_setting($hw_cmd);
		
	// 2022-07-18 + 補加指令
	$hw_cmd = array('op' => 'update', 'table' => 'room_electric_situation', 'id' => $id);
	insert_system_setting($hw_cmd);
	
	// 房間現況紀錄 -- 20200428
	$sql = "SELECT member_id FROM `room_electric_situation` WHERE member_id = '{$id}'";
	$tmp = $PDOLink->prepare($sql);
	$tmp->execute();
	$rs  = $tmp->fetch();
	$chk = $rs['member_id'];
	
	if($chk == '') {
		
		$elec_room_id = $room_id == '' ? '0' : $room_id;
		$sql = "INSERT INTO `room_electric_situation` (`id`, `member_id`, `room_id`, `powerstaus`, 
				`start_amonut`, `now_amount`, `start_balance`, `now_balance`, `start_date`, `update_date`) 
				VALUES (NULL, '{$id}', '{$elec_room_id}', '0', '0', '0', '0', '0', '{$nowtime}', '{$nowtime}')";
		$PDOLink->exec($sql);
		$new_id = $PDOLink->lastInsertId();
		
		$hw_cmd = array('op' => 'insert', 'table' => 'room_electric_situation', 'id' => $new_id);
		insert_system_setting($hw_cmd);
	}
	
	// 編號、房號、卡號、登入身分、餘額變更-- 20200813
	if($room_num != $room_num_old || $id_card != $id_card_old || $identity != $identity_old || $username != $username_old || $balance != $balance_old) {
		
		if($room_num != $room_num_old) {
			$sql = "SELECT id FROM `room` WHERE `name` IN ('{$room_num_old}', '{$room_num}')";
		} else {
			$sql = "SELECT id FROM `room` WHERE `name` = '{$room_num}'";
		}
		
		$tmp = $PDOLink->prepare($sql);
		$tmp->execute();
		$id_tmp  = $tmp->fetchAll();
		
		// 有異動需初始化
		foreach($id_tmp as $v) {
			$hw_cmd = array('op' => 'Single_Initialize', 'table' => 'room', 'id' => $v['id']);
			insert_system_setting($hw_cmd);
		}
	}
 	
	// log_list
	$content = "更新: [member] id:{$id}, 管理員:{$admin}";
	$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', '', '{$nowtime}'); ";
	$PDOLink->exec($log_ins_q);
	
	die(header("Location: ../member_edit.php?id={$id}&success=1"));
	
} else {
	
	die(header("Location: ../member_edit.php?id={$id}&error=1"));
	
}

?>       