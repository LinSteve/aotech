<?php

	include_once("../config/db.php");
	
	require '../vendor/autoload.php';
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;
	
	$mail = new PHPMailer(true); // Instantiation and passing `true` enables exceptions
	
	$sql = "SELECT * FROM `contact_list` WHERE `enable` = 1";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$mail_list = $rs->fetchAll();

	$sql = "SELECT * FROM `member` WHERE balance < 0";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	$subject   = "負值餘額通知";
	$mail_body = "";
	$tbody_str = "";
	$nowtime   = date('Y-m-d H:i:s');
	$remote_ip = $_SERVER['REMOTE_ADDR'];
	
	$table_str = "<table border='1' cellpadding='4' cellspacing='0'>
				  <thead><tr><th>姓名</th><th>學號</th><th>房號</th><th>負值餘額</th></tr>
				  </thead><tbody>%s</tbody></table>";
	$td_str    = "<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>";
	
	foreach($data as $v) {
		$tbody_str .= sprintf($td_str, $v['cname'], $v['username'], $v['room_strings'], $v['balance']);
	}
	
	$mail_body = sprintf($table_str, $tbody_str);

	if(isset($mail_list) & ($data != '')) {
		
		try {
			// Server settings
			// $mail->SMTPDebug  = SMTP::DEBUG_SERVER;              // Enable verbose debug output
			$mail->isSMTP();                                        // Send using SMTP
			$mail->Host 	  = MAIL_HOST;  	                    // Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                // Enable SMTP authentication
			$mail->Username   = MAIL_ACCOUNT;             			// SMTP username
			$mail->Password   = MAIL_PWD;      			            // SMTP password
			// $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
			$mail->Port       = MAIL_PORT;                          // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
			$mail->CharSet    = 'UTF-8';

			// Recipients
			$mail->setFrom('aotech.service@gmail.com', 'mailer');
			
			foreach($mail_list as $v) {
				$mail->addAddress($v['address'], $v['recipient']);     // Add a recipient
			}
			
			// $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
			// $mail->addAddress('ellen@example.com');               // Name is optional
			// $mail->addReplyTo('info@example.com', 'Information');
			// $mail->addCC('cc@example.com');
			// $mail->addBCC('bcc@example.com');

			// Attachments
			// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

			// Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = $subject;
			$mail->Body    = $mail_body;
			// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			$mail->send();
			
			// echo 'Message has been sent';
			
			// log_list
			$content = " 通知信發送完成";
			$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', 'remote_ip:{$remote_ip}', '{$nowtime}'); ";
			$PDOLink->exec($log_ins_q);
			
		} catch (Exception $e) {
			
			// echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			
			// log_list
			$content = " 通知信發送失敗 ".$mail->ErrorInfo;
			$log_ins_q = "INSERT INTO `log_list` (`content`, `data_type`, `add_date`) VALUES ('{$content}', 'remote_ip:{$remote_ip}', '{$nowtime}'); ";
			$PDOLink->exec($log_ins_q);
		}		
	}
?>