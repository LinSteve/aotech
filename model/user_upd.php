<?php 

	include_once('../config/db.php');
	include('../chk_log_in.php');
 
	$sn = $_POST['sn'];
	$id = $_SESSION['user']['id'];
	$OldPwd = $_POST['o_pwd'];
	$NewPwd = $_POST['new_pwd'];
	$CheckNewPwd = $_POST['new_pwd_check'];

	if($OldPwd == ''){
		header("location: ../admin_edit_student.php?error=1");
		exit();
	}

	// 變更密碼用
	// $list_q="select * from admin where id='".$id."' and pwd=password('".$OldPwd."') ";
	// $list_q = "SELECT * FROM `member` WHERE `id` = '".$id."' AND `password` = PASSWORD('".$OldPwd."') ";
	$list_q = "SELECT * FROM `member` WHERE `id` = ? AND `password` = CONCAT('*', UPPER(SHA1(UNHEX(SHA1(?))))) ";
	$list_r = $PDOLink->prepare($list_q); 
    $list_r->execute(array($id, $OldPwd));
    $rs = $list_r->fetch();

	// 存在
    if($rs) {

		if($NewPwd) {

			if($CheckNewPwd) {
				 
				// $upd_q = "UPDATE member SET `password` = password('{$CheckNewPwd}'), update_date = '{$now_time}' WHERE 1 AND id = '{$id}'";
				$upd_q = "UPDATE `member`  SET `password` = CONCAT('*', UPPER(SHA1(UNHEX(SHA1(?))))), update_date = now()  WHERE id = ?";
				$stmt = $PDOLink->prepare($upd_q);
				$stmt->execute(array($CheckNewPwd,$id));
				direct_to_index();
				exit();

			} else {
				
				header("location: ../admin_edit_student.php?error=3"); 
				exit();

			}

		} else {

			header("location: ../admin_edit_student.php?error=2"); 
			exit();

		}

	// 帳號不存在
	} else {

		header("location: ../admin_edit_student.php?error=1");
		exit();

	}
 
?>