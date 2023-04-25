<?php  
include_once('../../config/db.php');

$admin = $_SESSION['admin_user']['username'];
if(!$admin) func::alertMsg('請登入', '../session.php', true);

$center_id = array();
$param = array();
$where = "";
$dong = $_POST["dong"];
$floor = $_POST["floor"];
$mode = trim($_POST["price_floor_mode"]);
$rate = trim($_POST["price_floor_degree"]);
$mode_220 = trim($_POST["price_floor_mode_220"]);
$rate_220 = trim($_POST["price_floor_degree_220"]);
$ip = func::getUserIP();

if($dong == '' || $floor == '' ) die(header("Location: ../power-ratecharge.php?error=2"));
if($mode=='' && $rate == '') die(header("Location: ../power-ratecharge.php?error=3"));
if($dong) {
	$where .= " AND dong = :dong ";
	$param[':dong'] = $dong;
}

if($floor) {
	$where .= " AND floor = :floor ";
	$param[':floor'] = $floor;
}

if($mode && $mode_220) {
	$sql = "UPDATE `room` SET `mode`= :mode, mode_220= :mode_220, update_date = NOW() WHERE 1 ".$where;
	$param[':mode'] = $mode;
	$param[':mode_220'] = $mode_220;
	$update_mode = func::excSQLwithParam('update', $sql, $param, false, $PDOLink);	
	unset($param[':mode']);
	unset($param[':mode_220']);
}
if($rate && $rate_220) {
	$sql = "UPDATE `room` SET  price_degree=:rate ,  price_degree_220=:rate_220 ,  update_date = NOW() WHERE 1 ".$where;
	$param[':rate'] = $rate;
	$param[':rate_220'] = $rate_220;
	$update_rate = func::excSQLwithParam('update', $sql, $param, false, $PDOLink);	
	unset($param[':rate']);
	unset($param[':rate_220']);
}

$sql  = "SELECT center_id FROM `room` WHERE 1 {$where} GROUP BY center_id";
$data = func::excSQLwithParam('select', $sql, $param, true, $PDOLink);
$mode_chn = func:: powerMode($mode);
$mode_chn_220 = func:: powerMode_220($mode_220);
foreach($data as $v) {
	$center_id[] = $v['center_id'];
}

if($update_mode || $update_rate ) {	
	$field = array();
	$change = '';
	if($update_mode) { 
		$field[] = 'mode';
		$field[] = 'mode_220';
		$change .=" 110收費設定:{$mode_chn}; "; 		
		$change .=" 220收費設定:{$mode_chn_220}; "; 		
	}		
	if($update_rate) {
		$field[] = 'price_degree'; 
		$field[] = 'price_degree_220'; 
		$change .=" 110用電度數:{$rate}; ";
		$change .=" 220用電度數:{$rate_220}; ";
	}
	foreach($center_id as $v) { 
		//$sql = "SELECT id FROM room WHERE center_id = ".$v;
		$sql = "SELECT id FROM room WHERE center_id = ".$v." AND dong='".$dong."' ORDER BY dong,center_id LIMIT 1";
		echo 'sql:'.$sql.'<BR';
		$room_arr = func::excSQL($sql, $PDOLink, false);
		$room_id  = $room_arr['id'];
		 
		$hw_cmd = array('op' => 'update', 'table' => 'room', 'id' => $room_id, 'field' =>$field, 'mode' => 'layer'); // room_id
		insert_system_setting($hw_cmd);
		$hw_cmd = array('op' => 'Layer_ChangeMode', 'table' => 'room', 'id' => $v, 'field' =>$field);  // center_id
		//insert_system_setting($hw_cmd);
		insert_system_setting_for_dong($hw_cmd, $dong);
	}   
		$content = "整層樓房間設定: 棟別:{$dong}; 樓層:{$floor}; 管理員:{$admin}; ip: {$ip};".$change;
		func::toLog('後台', $content, $PDOLink);
		die(header("Location: ../power-ratecharge.php?success=1"));
	
} else { 
	die(header("Location: ../power-ratecharge.php?error=1")); 
} 

?>       