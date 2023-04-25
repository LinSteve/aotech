<?php 

include_once('../../config/db.php');

$admin    = $_SESSION['admin_user']['username'];
if(!$admin) func::alertMsg('請登入' , '../session.php',  true); 

$where = "";
$dong = $_POST['dong'];
$floor = $_POST['floor'];
$param = array();
$ip = func::getUserIP();

if($dong != '' && $floor != '')
{	
	if($dong)
	{
		$where .= " AND dong = :dong ";
		$param[':dong'] = $dong;
	} 
	if($floor) 
	{
		$where .= " AND floor = :floor ";
		$param[':floor'] = $floor;
	} 
	// var_dump($param);
	$sql   = "SELECT center_id, dong, floor FROM `room` WHERE 1 {$where} GROUP BY center_id; "; 
    $tmp = func::excSQLwithParam('select', $sql, $param, true, $PDOLink); // 一層樓會有兩個 center_id
	
	foreach($tmp as $v) { 
		$hw_cmd = array('op' => 'Layer_Initialize', 'table' => 'room', 'id' => $v['center_id']);	// 初始化 
		//insert_system_setting($hw_cmd, $PDOLink);
		insert_system_setting_for_dong($hw_cmd, $v['dong']);
	}
 
	$data = func::excSQLwithParam('select', $sql, $param, false, $PDOLink); 
	
	$content = "樓層初始化: dong:{$data['dong']},floor:{$data['floor']}, 管理員:{$admin}; ip :{$ip}";		 
	func::toLog('後台', $content, $PDOLink); 
	
	die(header("Location: ../power-room_initialize.php?success=1")); 
	
} else { 
	// if($dong != '' & $floor == '') 
	// { 
		// $hw_cmd = array('op' => 'ALL_Initialize', 'table' => 'room');
		// insert_system_setting_for_dong($hw_cmd, $dong); // for 棟別初始化  
        // $content = "棟別初始化, 管理員:{$admin}"; 
		// toLog('後台', $content, $PDOLink); 	

		// header("Location: ../power-room_initialize.php?success=1");
		// return;
	// } 
	die(header("Location: ../power-room_initialize.php?error=1"));
}
?>
 