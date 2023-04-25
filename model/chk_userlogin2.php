<?php 
include_once('../config/db.php');

//if(!isset($_POST['id']) || !isset($_POST['pwd'])){
if(!isset($_GET['id']) || !isset($_GET['pwd'])){
	//若沒有從Login submit或帳密為空白，就導回Login.php  
	header("location: ../index.php?error=1");

} else {

	$identity   = IDENTITY_USER;
	$member_id  = $_GET['id'];
	$member_pwd = $_GET['pwd'];
	
 
	// 因匯入資料的補零與編輯資料的補零的數目不同, 補九碼或十碼
	if(count($member_id) < 9 )	$member_id_9 = str_pad(trim($member_id),9,"0",STR_PAD_LEFT);
	if(count($member_id) < 10 )	$member_id_10 = str_pad(trim($member_id),10,"0",STR_PAD_LEFT);
 
	try {
		
		// $sql = "SELECT * FROM `member` WHERE 1 AND `identity` = '{$identity}' 
				// AND `username` = '{$member_id}' AND `password` = password('{$member_pwd}') ";
		// $sql = "SELECT * FROM `member` WHERE 1 AND `identity` = '{$identity}' 
		// AND `username` = :member_id AND `password` =  CONCAT('*', UPPER(SHA1(UNHEX(SHA1(:member_pwd)))))";		
		$sql = "SELECT * FROM `member` WHERE 1 AND `identity` IN({$identity}, 5)
		AND (`username` = :member_id_9 OR `username` = :member_id_10) AND `password` =  CONCAT('*', UPPER(SHA1(UNHEX(SHA1(:member_pwd)))))";		
		//echo 'sql:'.$sql.'<BR>';
		$stmt = $PDOLink->prepare($sql);
		$param = array(
		":member_id_9" => $member_id_9 ,
		":member_id_10" => $member_id_10 ,
		":member_pwd" =>$member_pwd 
		);		
		$stmt->execute($param);
		$result = $stmt->fetch();
		
	} catch(PDOException $e) {
		
		get_log_list($e->getMessage().' SQL ERROR : '.$sql);
		
		header("location: ../index.php?error=1");
		return;
	}
	
	if($result){
		//echo 'check_1<BR>';
		//die();
		$_SESSION['user']['id']       = $result['id'];
		$_SESSION['user']['username'] = $result['username']; 
		$_SESSION['user']['cname']    = $result['cname']; 
		$_SESSION['user']['pwd']      = $result['pwd'];
		$_SESSION['user']['identity'] = $result['identity'];

		 /* error_log php function */
		 $content = $result['username'].'-'.$result['cname']."；學生登入：".$_GET['captcha-response'];
		 get_log_list($content);

		 header("location: ../new-member2.php"); 
		 //登入後進到的頁面 header("location: home.php");
		 
	} else {
		
		//echo "<h1 style='color:red;'>帳號密碼錯誤，請重新登入。</h1>";
		//echo "<p><a href='admin_login.php'>回到登入畫面</a></p>";
		//header("location: ../index.php?error=1");
		echo  "<script type ='text/javascript'>alert('帳號密碼錯誤，請重新登入'); ";
		echo "location.href = '../index.php'</script>";	
		exit();
	}
 
}
?>