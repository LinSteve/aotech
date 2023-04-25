<?php 
include_once('../../config/db.php');

$dong   = $_POST['dong'];
$result = "<option value=''>全部</option>"; 
// $result = "<option value=''>請選擇</option>"; 
if($dong != '') 
{	
	$sql = "SELECT floor FROM `room` WHERE dong = ? GROUP BY floor "; 
	$tmp = func::excSQLwithParam('select', $sql, array($dong), true, $PDOLink);
	asort($tmp);
	
	foreach($tmp as $v) {
		$floor   = $v['floor'];
		$result .= "<option value='{$floor}'>{$floor}</option>";
	} 
} 
echo $result;
?>