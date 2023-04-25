<?php

$web_title = "【AOTECH】彰化縣政府智慧照明系統";

ini_set("display_errors", false); 				
error_reporting(E_ALL^E_NOTICE^E_WARNING);

date_default_timezone_set("Asia/Taipei");
session_cache_expire(28800);					
ini_set('session.gc_probability',100);

session_start();
ob_start();
ob_end_clean();

header("Cache-Control:no-cache,must-revalidate");
header("P3P: CP=".$_SERVER["HTTP_HOST"]."");     
header('Content-type: text/html; charset=utf-8');
header('Vary: Accept-Language');

$PDOLink = new_db_conn();

function new_db_conn() 
{	
	$PDOLink;
	$PDOHostVar       = 'localhost';
	$PDODBnameVar     = 'ndhu_db_new';
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

function old_db_conn() 
{	
	$PDOLink;
	$PDOHostVar       = 'localhost';
	$PDODBnameVar     = 'ndhu_db';
	$PDODBuserVar     = 'jack';
	$PDODBpasswordVar = '07101123';
	 
	
	try {   
		$PDOLink = new PDO("mysql:host={$PDOHostVar};dbname={$PDODBnameVar}",$PDODBuserVar,$PDODBpasswordVar);  
		$PDOLink->query("SET NAMES 'utf8'");
		$PDOLink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $e) {
		print "連線失敗" . $e->getMessage(); 
	}
	
	return $PDOLink;
}


?>