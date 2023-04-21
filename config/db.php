<?php
define('CONFIG_PATH', __DIR__);
require_once(CONFIG_PATH . "/class.func.php");
$web_title = "【AOTECH】智慧學校平台 ";

define('WEBADMIN', 'aoadmin');
define('DEF_PWD',  '88888');

define('IDENTITY_USER',  '0');
define('IDENTITY_ADMIN', '1');
define('ADMIN_LIMIT',    '10');

define('MENU_CHECK_ENABLE', true);

ini_set("display_errors", true); 								          //顯示 Error, true => 開, false => 關
error_reporting(E_ALL^E_NOTICE^E_WARNING);

date_default_timezone_set("Asia/Taipei");   					    //時區(亞洲/台北)
session_cache_expire(28800);											        //session逾時設定; 
ini_set('session.gc_probability',100); 	

session_start();
ob_start();								    					                  //可以解決header有先送出東西的問題
ob_end_clean();							    					                //先ob_start 再進行一次ob_end_clean

header("Cache-Control:no-cache,must-revalidate");   			//強迫更新
header("P3P: CP=".$_SERVER["HTTP_HOST"]."");        			//解決在frame中session不能使用的問題，可填ip或是domain
header('Content-type: text/html; charset=utf-8');				  //指定utf8編碼 
header('Vary: Accept-Language');

$PDOLink = db_conn();
$rs  = $PDOLink->query("SELECT * FROM smtp_info WHERE id = '1' ");
$row = $rs->fetch();
$MAIL_ACCOUNT = $row['mail_account'];
$MAIL_PWD = $row['mail_pwd'];
$MAIL_PORT = $row['mail_port'];
$MAIL_HOST = $row['mail_host'];
define('MAIL_ACCOUNT', $MAIL_ACCOUNT);
define('MAIL_PWD', $MAIL_PWD);
define('MAIL_PORT', $MAIL_PORT);
define('MAIL_HOST', $MAIL_HOST);

function db_conn() 
{	
	$PDOLink;
	$PDOHostVar       = '139.177.186.94:3306';
	// $PDODBnameVar     = 'ndhu_newtest';
	$PDODBnameVar     = 'ntut_db_new';
	$PDODBuserVar     = 'barry';
	$PDODBpasswordVar = '1qaz!@#$W';
	
	try {   
		$PDOLink = new PDO("mysql:host={$PDOHostVar};dbname={$PDODBnameVar}",$PDODBuserVar,$PDODBpasswordVar);  
		$PDOLink->query("SET NAMES 'utf8'");
		$PDOLink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $e) {
		print "連線失敗" . $e->getMessage(); 
	}
	
	return $PDOLink;
}

function get_log_list($content)
{
	/* 使用資料庫 */
    // $PDOLink = new PDO('mysql:host=localhost;dbname=ndhu_db','andy','aotech2018');  
	$PDOLink = db_conn();
    $PDOLink->query("SET NAMES 'utf8'");

    /* insert log history */
    //$date = date('d.m.Y h:i:s');
    $date = date("Y-m-d H:i"); 
     
    /* error_log php function */
    error_log($content."\n", 3, "/var/tmp/my-errors.log", "aotech.service@gmail.com");

        /* log db save */
        try {
            $col="`content`,`data_type`,`add_date`";
            $col_data="'".$content."','1','".$date."' ";
            $ins_q="insert into log_list (".$col.") values (".$col_data.") ";
            $PDOLink->exec($ins_q); 
        }

        catch(PDOException $e){
           echo $ins_q . "<br>" . $e->getMessage();
        }

}

// -- 20200707
function insert_system_setting_for_dong($hw_cmd, $dong) 
{	
	$PDOLink = db_conn();
	$nowtime = date('Y-m-d H:i:s');
	
	$sql = "SELECT `name` FROM `host` WHERE dong = '{$dong}'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$temp_arr = $rs->fetchAll();
	$dong_arr = array();
	
	foreach($temp_arr as $v) {
		$dong_arr[] = $v['name'];
	}

	$c_code  = json_encode($hw_cmd);

	$sql_hw  = "INSERT INTO `system_setting` (`title`, `computer_name`, `c_code`, `M0`, `M1`, `M2`, `M3`, `M4`, `M5`, `add_date`) 
				VALUES ('', 'Web', '{$c_code}', 
				'".(in_array("M0", $dong_arr) ? "0" : "1")."', 
				'".(in_array("M1", $dong_arr) ? "0" : "1")."', 
				'".(in_array("M2", $dong_arr) ? "0" : "1")."', 
				'".(in_array("M3", $dong_arr) ? "0" : "1")."',
				'".(in_array("M4", $dong_arr) ? "0" : "1")."',
				'".(in_array("M5", $dong_arr) ? "0" : "1")."', '{$nowtime}')";
	
	$PDOLink->exec($sql_hw);
}

function insert_system_setting($hw_cmd) 
{	
	// -- op command list --
	// Single_Initialize
	// Single_ChangeMode
	// ALL_Initialize
	// ALL_ChangeMode
	// Layer_ChangeMode
	// Layer_Initialize
	// MoveOut_All            清空名單
	// MemberGroup_Initialize 修改名單資料
	// GroupAuthorityModify   群組編輯
	// GroupDelete            群組刪除
	// Reload_SystemSetting  
	// OpenRoomDoor
	// Reset_Hardware         重啟
	$c_code  = json_encode($hw_cmd);
	$nowtime = date('Y-m-d H:i:s');
	$sql_hw  = "INSERT INTO `system_setting` (`title`, `computer_name`, `c_code`, `M0`, `M1`, `M2`, `M3`, `M4`, `M5`, `add_date`) 
				VALUES ('', 'Web', '{$c_code}', '0', '0', '0', '0', '0', '0', '{$nowtime}')";
	$PDOLink = db_conn();
	$PDOLink->exec($sql_hw);
}

?>