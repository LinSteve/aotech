<?php 
include_once('../../config/db.php');

$admin = $_SESSION['admin_user']['username'];
if(!$admin) func::alertMsg('請登入', '../session.php', true);

$ip = func::getUserIP();

$mode  = trim($_POST["price_all_mode"]);
$rate = trim($_POST["price_all_degree"]);
$mode_220  = trim($_POST["price_all_mode_220"]);
$rate_220 = trim($_POST["price_all_degree_220"]);
if($mode == '' && $rate == '' && $mode_220 == '' && $rate_220 == '') die(header("Location: ../power-ratecharge.php?error=3"));

if($mode && $mode_220)
{
	$sql = "UPDATE `room` SET `mode`=? , mode_220=? , update_date=NOW() ;"; 
	$updated_mode =  func::excSQLwithParam('update', $sql, array($mode, $mode_220), false, $PDOLink);	 
}
if($rate && $rate_220)
{
	$sql = "UPDATE `room` SET price_degree=? , price_degree_220=? , update_date=NOW() ;"; 
	$updated_rate =  func::excSQLwithParam('update', $sql, array($rate, $rate_220), false, $PDOLink);		 
} 
$field = array(); 
$change = '';
if($updated_mode || $updated_rate) 
{	  
	if($updated_mode)
	{
		$field[] = 'mode';
		$field[] = 'mode_220';
		$mode_chn = func::powerMode($mode); 
		$mode_chn_220 = func:: powerMode_220($mode_220);
		$change .= " 110收費設定 : {$mode_chn}; ";  
		$change .= " 220收費設定 : {$mode_chn_220}; ";  
	}
	if($updated_rate)
	{
		$field[] = 'price_degree';
		$change .=" 110用電度數:{$rate}; ";
		$change .=" 220用電度數:{$rate_220}; ";
	}
	$hw_cmd = array('op' => 'update', 'table' => 'room', 'id' => '1', 'field' => $field, 'mode' => 'all');
	insert_system_setting($hw_cmd); 
	$hw_cmd = array('op' => 'ALL_ChangeMode', 'table' => 'room', 'id' => '1' ,'field' =>$field);
	insert_system_setting($hw_cmd);
	
	$content = "全部房間設定: 管理員:{$admin}; ip:{$ip}; ".$change; 
	func::toLog('後台', $content, $PDOLink);	 			
	die(header("Location: ../power-ratecharge.php?success=1"));
	
} else {
	die(header("Location: ../power-ratecharge.php?error=1"));
}

?>       