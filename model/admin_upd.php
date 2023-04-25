<?php 

	include_once('../config/db.php');
	include('../chk_log_in.php');
 
	$id = $_SESSION['admin_user']['id'];
 
	$OldPwd = $_POST['o_pwd'];
	$NewPwd = $_POST['new_pwd'];
	$CheckNewPwd = $_POST['new_pwd_check'];

	if($OldPwd == ''){
		header("location: ../admin_edit.php?error=1");
		exit();
	}

	// 變更密碼用
	// $list_q="select * from admin where id='".$id."' and pwd=password('".$OldPwd."') ";
	// $list_q = "SELECT * FROM `member` WHERE `id` = '".$id."' AND `password` = PASSWORD('".$OldPwd."')";
 	$sql = "SELECT * FROM `member` WHERE `id` = ? AND `password`= CONCAT('*', UPPER(SHA1(UNHEX(SHA1(?))))) ";
	$stmt = $PDOLink->prepare($sql); 
    $stmt->execute(array($id , $OldPwd));
    $data = $stmt->fetch();

	// 存在
    if($data) {

		if($NewPwd) {

			if($CheckNewPwd) {
				
				$now_time = date($date_format.' '.$time_format);
				// $upd_q = "UPDATE member SET `password` = password('{$CheckNewPwd}'), update_date = '{$now_time}' WHERE 1 AND id = '{$id}'";
				$sql = "UPDATE member SET `password` =  CONCAT('*', UPPER(SHA1(UNHEX(SHA1(?))))), update_date = now() WHERE  id = ? ";
				$stmt = $PDOLink->prepare($sql);
				$stmt->execute(array($CheckNewPwd, $id));
				direct_to_index();
				exit();

			} else {
				
				header("location: ../admin_edit.php?error=3"); 
				exit();

			}

		} else {

			header("location: ../admin_edit.php?error=2"); 
			exit();

		}

	// 帳號不存在
	} else {

		header("location: ../admin_edit.php?error=1");
		exit();

	}

?>