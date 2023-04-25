<?php 
define("SITE_KEY", "6LdfXCklAAAAACuqgdBBsz9lpfkgCZqd-335vyz7");
define("SECRET_KEY", "6LdfXCklAAAAAPdlEtagQRtvfbX560Gneg-w2GFo");
	if (isset($_POST['captcha-response']) && ! empty($_POST['captcha-response'])) {
		$data = array(
            'secret' => SECRET_KEY,
            'response' => $_POST['captcha-response']
        );
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);
        if ($response == true) {
			die(header("location: ./chk_adminlogin2.php?id=".$_POST['id']."&pwd=".$_POST['pwd']."&captcha-response=".$_POST['captcha-response'])); 
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

?>