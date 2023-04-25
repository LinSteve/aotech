<?php
/* 登出 */

// $LoginDataType = $_GET[data_type];

/*
if ($LoginDataType == 'member') {

	session_start();
	unset($_SESSION[user]);
	header("location: home.php");

} elseif ($LoginDataType == 'admin') {

	session_start();
	unset($_SESSION[admin_user]);
	header("location: index.php");

}
*/

session_start();

unset($_SESSION['admin_user']);
unset($_SESSION['user']);

header("location: session.php");
?>