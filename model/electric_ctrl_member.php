<?PHP
	include_once('../config/db.php');
	$admin = $_SESSION['admin_user']['username'];
	// if(!$admin ) alertMsg('請登入', '../session.php', true);

	$id = $_GET['id'];
	
	if($id) {
		$sql = "SELECT `id`, room_strings, cname FROM `member` 
				WHERE `id` = ? AND trim(room_strings) <> '' AND del_mark = 0 LIMIT 1;";
		$stmt = $PDOLink -> prepare($sql);
		$stmt -> execute(array($id)); 
		$id_data =$stmt -> fetch();
		if($id_data) {
			$hw_cmd = array('op' => 'PowerOn', 'table' => 'member', 'id' => $id_data['id']);
			insert_system_setting($hw_cmd);
			$content = "控電人員設定; member: {$id_data['id']}; name: {$id_data['cname']}; room: {$id_data['room_strings']}; 管理員: {$admin}";
			func::toLog('前台', $content, $PDOLink); 	
			die(header("Location: ../new-member2.php?success=1"));			
		} else die(header("Location: ../new-member2.php?error=1"));			
	} else die(header("Location: ../new-member2.php?error=1"));			

?>