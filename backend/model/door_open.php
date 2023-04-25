<?php 
	include_once('../../config/db.php');
	$admin = $_SESSION['admin_user']['username']; 
	if(!$admin) func::alertMsg('請登入', '../session.php', true);
	$this_page = 'backend/model/door_open.php';
	$ip = func::getUserIP();

	if($_GET['type'] == 'single') {// 單間
		$sql ="SELECT id ,name FROM `room` WHERE id = ? ";
		$id_data = func::excSQLwithParam('select', $sql, array($_GET["id"]), false, $PDOLink);     
		
		if($id_data['id']) 
		{
			$hw_cmd = array('op' => 'OpenRoomDoor', 'table' => 'room', 'id' => $id_data['id']);		
			func::insertSystemSetting($hw_cmd, $PDOLink);
			
			$content = "單間房間開門, id:{$id_data['id']}; 房號:{$id_data['name']} ; 管理員:{$admin}; ip:{$ip}; path{$this_page}";
			func::toLog('後台', $content, $PDOLink); 		
			die(header("Location: ../curfew-door_initialize.php?success=1"));
		}else{
			die(header("Location: ../curfew-door_initialize.php?error=1"));
		}
	}

	if($_GET['type'] == 'floor') {// 整層
		$where = '';
		$param = array();
	
		if($_GET['dong'] && $_GET['dong'] != ''  )
		{
			$sql = "SELECT dong FROM `room` WHERE dong = ? GROUP BY dong";
			$dong_data = func::excSQLwithParam('select', $sql, array($_GET['dong']), false, $PDOLink);     
			if($dong_data['dong'] ) {
				$where .= " AND dong=:dong ";
				$param[':dong'] = $dong_data['dong'];
				
				if($_GET['floor'] && $_GET['floor'] != '') {
					$where .= " AND floor=:floor ";
					$param[':floor'] = $_GET['floor'];
				}
				$sql = " SELECT DISTINCT id FROM `room` WHERE 1 {$where} ORDER BY id ASC " ;
				$data = func::excSQLwithParam('select', $sql, $param, true, $PDOLink);     
				foreach($data as $row) {
					$hw_cmd = array('op' => 'OpenRoomDoor', 'table' => 'room', 'id' => $row['id']);
					func::insertSystemSetting($hw_cmd, $PDOLink) ;
				}
				if($_GET['floor'] == '') $floor = '全部';
				else $floor = $_GET['floor'];
				$content = "棟別&樓層房門開啟, 棟別:{$dong_data['dong']}; 樓層:{$floor} ; 管理員:{$admin}; ip:{$ip}; path{$this_page}";
				func::toLog('後台', $content, $PDOLink); 
				die(header("Location: ../curfew-door_initialize.php?success=1"));		
				// echo '1-1'; exit;
			} else {
				die(header("Location: ../curfew-door_initialize.php?error=1"));
			}
		} else {
			die(header("Location: ../curfew-door_initialize.php?error=2"));
		}
	} 
?>