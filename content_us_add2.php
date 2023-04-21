<?php 
ini_set('display_errors', 1);

error_reporting(E_ALL ^ E_NOTICE);

error_reporting(E_ALL ^ E_WARNING);


	include_once("config/db.php");
	// include("phpmailer/PHPMailerAutoload.php"); 			
	
	require 'vendor/autoload.php';
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;
	
	$mail = new PHPMailer(true); // Instantiation and passing `true` enables exceptions	

	$NowTime     = date("Y-m-d H:i:s");
	$curr_date   = date('Y-m-d');

	$title       = $_GET['title'];
	$phone       = $_GET['phone']; 
	$email       = $_GET['email'];
	
//	$myallsport  = implode ("，", $_GET['host_type']);
	$myallsport  = $_GET['host_type'];
	$host_other  = $_GET['host_other'];

//	$myallsport2 = implode("，", $_GET['room_type']);
	$myallsport2 = $_GET['room_type'];
	$room_other  = $_GET['room_other'];
	$data_type   = $_GET['data_type'];
	$room_number = $_GET['room_number'];
	$username    = $_GET['username_number'];
	// 房號檢查 -- 20200917
	$sql = "SELECT * FROM room WHERE `name` = '{$room_number}'";
	$rs  = $PDOLink->prepare($sql);  
	$rs->execute();
	$tmp = $rs->fetch();
	$dong = $tmp['dong'];
	$room_name = $tmp['name'];
	
	if($room_name == '') {
		header("location: content_us.php?error=5");
		exit();
	}
	
	// 學號檢查 -- 20200911
	$sql = "SELECT * FROM member WHERE username = '{$username}'";
	$rs  = $PDOLink->prepare($sql);  
	$rs->execute();
	$userRow = $rs->fetch();  
	$chkid   = $userRow['id'];
	
	if($chkid != '') {
		
		// 限制 1 min -- 20200911
		$sql = "SELECT add_date FROM content_us WHERE username_number = '{$username}' ORDER BY id DESC LIMIT 0, 1";
		$rs  = $PDOLink->prepare($sql);  
		$rs->execute();
		$tmp = $rs->fetch();
		$last_time = strtotime($tmp['add_date']);
		$now_time = time();

		if($now_time - $last_time < 60) {
			header("location: content_us.php?error=4"); 
			exit();
		}

	} else {
		header("location: content_us.php?error=1");
		exit();
	}

	if($myallsport or $host_other or $myallsport2 or $room_other or $data_type) {
			
		try {
			$mail->isSMTP();                                            // Send using SMTP
			$mail->Host 	  = MAIL_HOST;  	                    	// Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
			$mail->SMTPSecure = "ssl";                    // Gmail的SMTP主機需要使用SSL連線
			$mail->Username   = MAIL_ACCOUNT;
			$mail->Password   = MAIL_PWD;
			// $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
			$mail->Port       = MAIL_PORT;                              // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
			$mail->CharSet    = 'UTF-8';

			// Recipients
			$mail->setFrom(MAIL_ACCOUNT, 'mailer');
			
			$mail->isHTML(true);                                        // Set email format to HTML
			$mail->Subject = "【AOTECH】智慧學校平台 北科大學校客服"; 	// 郵件標題
			$mail->Body = "<hr>日期：".$NowTime."<br>宿舍房號：".$room_number."<br>棟別：".$dong."<br>學號：".$username."<br>姓名：".$title.
						  "<br>儲值主機操作：".$myallsport."<br>其他說明：".$host_other."<br>房內卡機使用：".$myallsport2."<br>其他說明：".$room_other."<br>"; 				    									   //設定郵件內容 
							  
			$mail->AddAddress(MAIL_ACCOUNT, "ao");
			//$mail->addBCC("barry@aotech.com.tw",   "Barry");
			//$mail->addBCC("emily@aotech.com.tw",   "Emily");
			//$mail->addBCC("a120216363@gmail.com",  "浩軒");
			$mail->addBCC("rsun329@hotmail.com",  "Steve");
			//$mail->addBCC("ao.patty887@gmail.com", "Patty");
			//$mail->addBCC("vivi@aotech.com.tw",    "佳怡");
			$mail->addBCC("irene@aotech.com.tw",    "祥玉");

			if(!$mail->send()) {
				header("location: content_us.php?error=2");
				exit();
			}

			$col="`title`,`username_number`,`room_number`,`phone`,`contact`,`email`,`status`,`host_type`,`host_other`,`room_type`,`room_other`,`user_id`,`data_type`,`add_date`,`del_mark`";
			$col_data="'".$title."','".$username."','".$room_number."','".$phone."','','".$email."','Y','".$myallsport."','".$host_other."','".$myallsport2."','".$room_other."','1','1','".$NowTime."','0'";
			$ins_q="insert into content_us (".$col.") values (".$col_data.") ";
			$PDOLink->exec($ins_q); 
			
			header("location: content_us.php?success=1");
				
		} catch(PDOException $e) {
			echo $e->getMessage();
		}

	} else {

		header("location: content_us.php?error=3"); 
		exit();
	}

	$PDOLink = null;
?>