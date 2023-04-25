<?php
include_once('../../config/db.php');
$admin = $_SESSION['admin_user']['username'];
//if(!$admin) func::alertMsg('請登入' , '../session.php', true);

$dong = $_POST['dong']; 
$result = "<option value=''>請選擇</option>";

if($dong) 
{
	$sql = "SELECT DISTINCT floor FROM `room` WHERE dong = ?"; 
	$data = func::excSQLwithParam('select', $sql, array($dong), true, $PDOLink);
 
	foreach($data as $v) { 
		$result .= "<option value='{$v['floor']}'>{$v['floor']}</option>";
	} 
}  
echo $result; 
?>
 