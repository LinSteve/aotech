<?php 
//include_once('../config/db.php');
define("SITE_KEY", "6LdfXCklAAAAACuqgdBBsz9lpfkgCZqd-335vyz7");
define("SECRET_KEY", "6LdfXCklAAAAAPdlEtagQRtvfbX560Gneg-w2GFo");
	if (isset($_POST['captcha-response2']) && ! empty($_POST['captcha-response2'])) {
		$data = array(
            'secret' => SECRET_KEY,
            'response' => $_POST['captcha-response2']
        );
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);
        if ($response == true) {
			die(header("location: ./chk_adminlogin2.php?id=".$_POST['id']."&pwd=".$_POST['pwd']."&captcha-response=".$_POST['captcha-response2'])); 
		//    $result = '<div class="success">Your request has been successfully submitted</div>';
        //    echo $result;
        } else {
            $result = '<div class="error">Verification failed, please try again</div>';
            echo $result;
        }
    } else {
	    $result = '<div class="error">Verification failed, please try again</div>';
        echo $result;
    }
/*
if(!isset($_POST['id']) || !isset($_POST['pwd'])){
	//若沒有從Login submit或帳密為空白，就導回Login.php  
	die(header("location: ../index.php?error=1"));

} else {
	
	$identity  = IDENTITY_ADMIN;
	$admin_id  = $_POST['id'];
	$admin_pwd = $_POST['pwd'];

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
/*		 $content = $result['id']."；管理員登入".$_POST['captcha-response']."；$response=".$response;
		 get_log_list($content);
	//	 die(header("location: ../index.php?response=".$response));

		 die(header("location: ../new-member2.php")); 
*/		 //登入後進到的頁面 header("location: home.php");
		 
/*	} else {
		
		//echo "<h1 style='color:red;'>帳號密碼錯誤，請重新登入。</h1>";
		//echo "<p><a href='admin_login.php'>回到登入畫面</a></p>";
		//die(header("location: ../index.php?error=1"));
		echo  "<script type ='text/javascript'>alert('帳號密碼錯誤，請重新登入'); ";
		echo "location.href = '../index.php'</script>";	
		exit();
	}

}*/
?>