<?php 

	$user_sn  = '';
	$admin_id = '';

	if(isset($_SESSION['user']['id'])) {
		$identity = IDENTITY_USER;
		$sql_chk  = "SELECT * FROM member WHERE identity IN({$identity},5) AND id = '".$_SESSION['user']['id']."'";
		$rs_chk   = $PDOLink->prepare($sql_chk); 
	    $rs_chk->execute();
	    $row_chk  = $rs_chk->fetch();
		$user_sn  = $row_chk['id'];
	}
	  
	if(isset($_SESSION['admin_user']['id'])) {
		$identity = IDENTITY_ADMIN;
		$sql_chk  = "SELECT * FROM member WHERE identity = '{$identity}' AND id = '".$_SESSION['admin_user']['id']."'";
		$rs_chk   = $PDOLink->prepare($sql_chk); 
	    $rs_chk->execute();
	    $row_chk  = $rs_chk->fetch();
		$admin_id = $row_chk['id'];
	}

	if(!$user_sn && !$admin_id) {
		
		direct_to_index();
	}
	
	// 抓權限 -- 20200423	
	$group_data = json_decode($row_chk['group_id']);
	
	if($group_data) {
		
		$menu_access;
		
		$sql_chk = "SELECT menu_access FROM `group` WHERE id IN (". implode(',', array_map('intval', $group_data)) .")";
		$rs_chk  = $PDOLink->prepare($sql_chk);
		$rs_chk->execute();
		$tmp = $rs_chk->fetchAll();
		
		foreach($tmp as $row_tmp) {
			$inner = json_decode($row_tmp['menu_access']);
			foreach($inner as $v) {
				$menu_access[$v] = $v;
			}
		}
		
		$_SESSION['MENU_ACCESS'] = $menu_access;
	}

	// icon 權限 -- 20200511
	function check_access_icon($program_id) {
		
		if($_SESSION['admin_user']['username'] == WEBADMIN) {
			
			return true;
			
		} else if(MENU_CHECK_ENABLE) {
			
			$access = false;
			
			if(isset($_SESSION['MENU_ACCESS'])) {		
				$access = in_array($program_id, $_SESSION['MENU_ACCESS']);
			}
			
			return $access;
			
		} else {
			
			return true;
		}
	}
	
	// 檢查權限 -- 20200423
	function check_access($program_id) {
		
		if($_SESSION['admin_user']['username'] == WEBADMIN) {
			
			return true;
			
		} else if(MENU_CHECK_ENABLE) {
			
			$access = false;
			
			if(isset($_SESSION['MENU_ACCESS'])) {		
				$access = in_array($program_id, $_SESSION['MENU_ACCESS']);
			}
			
			if(!$access) {
				direct_to_main();
			}
			
		} else {
			
			direct_to_index();
		}
	}
	
	function direct_to_main() {
		
		header("location: new-member2.php?error=5"); // 先保留 -- warning headers already sent
		echo "<script>location.replace('new-member2.php')</script>";
		
		exit();
	}
	
	function direct_to_index() {
		
		unset($_SESSION['user']);		
		unset($_SESSION['admin_user']);
		unset($_SESSION['MENU_ACCESS']);
		
	//	header("location: index.php"); // 先保留 -- warning headers already sent
	//	echo "<script>location.replace('index.php')</script>";
	    echo  "<script type ='text/javascript'>alert('請先登入!!'); ";
		echo "location.href = 'index.php'</script>";	
		
		exit();
	}
?>