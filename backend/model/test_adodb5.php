<?php
/************************ YOUR DATABASE CONNECTION START HERE   ****************************/
		$DB_Server = "localhost"; // MySQL Server
		$DB_Username = "barry"; // MySQL Username
		$DB_Password = "1qaz!@#$W"; // MySQL Password
		$DB_DBName = "ndhu_db_new"; // MySQL Database Name
		 
		/***** DO NOT EDIT BELOW LINES *****/
		// Create MySQL connection
		$Connect = @mysql_connect($DB_Server, $DB_Username, $DB_Password) or die("Failed to connect to MySQL:<br />" . mysql_error() . "<br />" . mysql_errno());
		mysql_query("SET NAMES 'big5'");
		// Select database
		$Db = @mysql_select_db($DB_DBName, $Connect) or die("Failed to select database:<br />" . mysql_error(). "<br />" . mysql_errno());
/*
$db = adoNewConnection($driver); # eg. 'mysqli' or 'oci8'
$db->debug = true;
$db->connect($server, $user, $password, $database);
*/		
/*		include("adodb5/adodb.inc.php");
		$conn = ADONewConnection('mysql'); 
		$conn->debug = true;
		$conn->Connect("localhost", "barry", "1qaz!@#$W", "ndhu_db_new");
		$conn->query("SET NAMES 'utf8'");
		if (!$conn) { echo "無法連接資料庫 db1"; exit; }
*/
/*				
$sql="insert into twcms_courseitem (chr,post_year,post_month,sno,xtable,xtype,cu_seq,height,height_ref,width,width_ref,weight,label,orderno,xinner,insp,rough,stus,def,hrb,spec,quality,steel) values('".$chr."', '".$year."', '".$month."', '".$sno."', '".$xtable."', '".$xtype."', '".$cu_seq."', '".$height."', '".$height_ref."', '".$width."', '".$width_ref."', '".$weight."', '".$label."', '".$orderno."', '".$xinner."', '".$insp."', '".$rough."', '".$stus."', '".$def."', '".$hrb."', '".$spec."', '".$quality."', '".$steel."');";
			$insertTable=mysql_query($sql);
			if($insertTable==1){
				$cnt=$cnt+1;
			}
			echo mysql_error();
*/			
/************************ YOUR DATABASE CONNECTION END HERE  *******************************/

/*		include("adodb5/adodb.inc.php");
		$conn = ADONewConnection('mysql'); 
		$conn->debug = true;
		$conn->Connect("localhost", "barry", "1qaz!@#$W", "ndhu_db_new");
		$conn->query("SET NAMES 'utf8'");
		if (!$conn) { echo "無法連接資料庫 db1"; exit; }
*/