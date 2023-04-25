<?php
include_once('../config/db.php');

//if(!isset($_POST['id']) || !isset($_POST['pwd'])){
if(!isset($_GET['id']) || !isset($_GET['pwd'])){
	//若沒有從Login submit或帳密為空白，就導回Login.php  
	die(header("location: ../index.php?error=1"));

} else {
	
	$identity  = IDENTITY_ADMIN;
	$admin_id  = $_GET['id'];
	$admin_pwd = $_GET['pwd'];

	try {
		
		// $sql = "SELECT * FROM `member` WHERE del_mark = '0' AND `identity` = '{$identity}'
				// AND `username` = '{$admin_id}' AND `password` = password('{$admin_pwd}') ";
		$sql = "
			SELECT * FROM `member` 
			WHERE del_mark = '0' AND `identity` = '{$identity}' AND `username` = :admin_id AND `password` = CONCAT('*', UPPER(SHA1(UNHEX(SHA1(:admin_pwd)))))
			";		
		$stmt = $PDOLink->prepare($sql);
		$param = array(
		":admin_id" => $admin_id ,
		":admin_pwd" =>$admin_pwd 
		);
		$stmt->execute($param);
		$result = $stmt->fetch();
		
	} catch(PDOException $e) {
		
		get_log_list($e->getMessage().' SQL ERROR : '.$sql);
		
		header("location: ../index.php?error=1");
		return;
	}
	
	if($result){
		$_SESSION['admin_user']['id']       = $result['id'];
		$_SESSION['admin_user']['username'] = $result['username']; 
		$_SESSION['admin_user']['cname']    = $result['cname']; 
		$_SESSION['admin_user']['pwd']      = $result['pwd'];
		$_SESSION['admin_user']['identity'] = $result['identity'];

		 /* error_log php function */
		 $content = $result['id']."；管理員登入".$_GET['captcha-response'];
	//	 $content = $result['id']."；管理員登入".$_POST['captcha-response']."；$response=".$response;
		 get_log_list($content);
	//	 die(header("location: ../index.php?response=".$response));

		 die(header("location: ../new-member2.php")); 
		 //登入後進到的頁面 header("location: home.php");
		 
	} else {
		
		//echo "<h1 style='color:red;'>帳號密碼錯誤，請重新登入。</h1>";
		//echo "<p><a href='admin_login.php'>回到登入畫面</a></p>";
		//die(header("location: ../index.php?error=1"));
		echo  "<script type ='text/javascript'>alert('帳號密碼錯誤，請重新登入'); ";
		echo "location.href = '../index.php'</script>";	
		exit();
	}
}
?>