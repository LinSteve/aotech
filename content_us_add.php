<?php 
include_once('config/db.php');
$rs  = $PDOLink->query("SELECT * FROM recaptcha_key WHERE id = '1' ");
$row = $rs->fetch();
$SITE_KEY = $row['site_key'];
$SECRET_KEY = $row['secret_key'];
define("SITE_KEY", $SITE_KEY);
define("SECRET_KEY", $SECRET_KEY);
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
			$myallsport  = implode ("，", $_POST['host_type']);
			$myallsport2 = implode("，", $_POST['room_type']);
			die(header("location: ./content_us_add2.php?title=".$_POST['title']."&phone=".$_POST['phone']."&email=".$_POST['email']."&host_type=".$myallsport."&host_other=".$_POST['host_other']."&room_type=".$myallsport2."&room_other=".$_POST['room_other']."&data_type=".$_POST['data_type']."&room_number=".$_POST['room_number']."&username_number=".$_POST['username_number'])); 
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