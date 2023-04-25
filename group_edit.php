<?php 
	include('header_layout.php');
	include('nav.php'); 
	include('chk_log_in.php');
	
	$list_q = "SELECT * FROM `group` WHERE id = '".$_GET['id']."'";
	$list_r = $PDOLink->prepare($list_q);
	$list_r->execute();
	$row = $list_r->fetch();
	
	if($row == "") {
		// header('Location: new-editmember.php?error=2');
		echo "<script>location.replace('new-editgroup.php?error=2')</script>";
		return;
	}
	
	for($i = 1; $i <= 6; $i++) {
		eval("\$opt".$i." = '';");
	}

	$sql = "SELECT * FROM `menu_list` WHERE id NOT IN (7,8,11)";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$tmp = $rs->fetchAll();

	foreach($tmp as $v) {
		
		$id        = $v['id'];
		$category  = $v['category'];
		$item_name = $v['item_name'];
		$menu_arr  = json_decode($row['menu_access']);
		
		switch($category) {
			
			case '1':
				$select = in_array($id, $menu_arr) ? 'selected' : '';
				$opt1 .= "<option value='{$id}' {$select}>{$item_name}</option>";
				break;
			case '2':
				$select = in_array($id, $menu_arr) ? 'selected' : '';
				$opt2 .= "<option value='{$id}' {$select}>{$item_name}</option>";
				break;
			case '3':
				$select = in_array($id, $menu_arr) ? 'selected' : '';
				$opt3 .= "<option value='{$id}' {$select}>{$item_name}</option>";
				break;
			default:
				break;
		}
	}

	$sql = "SELECT * FROM `door`";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$tmp = $rs->fetchAll();
	$door_arr  = json_decode($row['door_id']);

	foreach($tmp as $v) {
		
		$id        = $v['id'];
		$item_name = $v['name'];
		$select    = in_array($id, $door_arr) ? 'selected' : '';
		
		$opt4 .= "<option value='{$id}' {$select}>{$item_name}</option>";
	}

	$sql = "SELECT * FROM `elevator`";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$tmp = $rs->fetchAll();
	$elevator_arr  = json_decode($row['elevator_id']);

	foreach($tmp as $v) {
		
		$id        = $v['id'];
		$item_name = $v['name'];
		$select    = in_array($id, $elevator_arr) ? 'selected' : '';
		
		$opt5 .= "<option value='{$id}' {$select}>{$item_name}</option>";
	}

	// $sql = "SELECT * FROM `room`";
	// $rs  = $PDOLink->prepare($sql);
	// $rs->execute();
	// $tmp = $rs->fetchAll();	

	// foreach($tmp as $v) {
		
		// $id        = $v['id'];
		// $item_name = $v['name'];
		
		// $opt6 .= "<option value='{$id}'>{$item_name}</option>";
	// }
	
	$opt6_str = array();
	
	if($row['room_id'] != '') {
		
		$sql_in   = '';
		$room_arr = json_decode($row['room_id']);
		
		if($room_arr) {
			
			$sql_in = implode(',', $room_arr);
			
			$sql = "SELECT `name` FROM `room` WHERE id IN ({$sql_in})";
			$rs  = $PDOLink->prepare($sql);
			$rs->execute();
			$tmp = $rs->fetchAll();
			
			foreach($tmp as $v) {
				$opt6_str[] = $v['name'];
			}	
		}
		
		$opt6 = implode(',', $opt6_str);
	}
?>
<section id="main" class="wrapper">
<div class="col-12 btn-back"><a href="new-editgroup.php" ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="row container-fluid mar-bot50">
	
	<?php if($_GET['success'] == 1){ ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-success col-lg-9" role="alert">
		  <strong>設定完成!!</strong> 
		</div>
	<?php } else if($_GET['error'] == 1){ ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger  col-lg-9" role="alert">
		  <strong><?php echo $lang->line("index.Incorrect-or-non-existent-data");?></strong> 
		</div>
	<?php } else if($_GET['error'] == 2){ ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger  col-lg-9" role="alert">
		  <strong>房號人數已達上限!!</strong> 
		</div>
	<?php } else if($_GET['error'] == 3){ ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger  col-lg-9" role="alert">
		  <strong>學號或卡號有重覆，查詢可請到非住宿生查看!! </strong> 
		</div>
	<?php } ?> 
	</div>
	
	<div class="inner">
		<div class="container">
			<div class="row">

			<!-- 	<a href="RoomStatus3.php"><i class="fas fa-chevron-circle-left fa-3x"></i></a><br> -->
				<div class="col-12">
					<h1 class="jumbotron-heading text-center">群組編輯</h1>
					<form action='model/group_upd.php' method='post'>

						<div class="form-group row">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right " >群組名稱</label>
							<div class="col-sm-9"> 
								<input   readonly="readonly" type="text" class="form-control  col"  name="grp_name" placeholder="群組名稱" value="<?php echo $row['name'] ?>">  
							</div>
						</div>

						<div class="form-group row">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right">用途說明</label>
    						<div class="col-sm-9"> 
								<input   type="text"  maxlength='30' class="form-control  col" name="grp_usage" value="<?php echo $row['usage'] ?>">  
    						</div>
						</div>
<!-- 後台權限複選  -->
						<?php
						if($_SESSION['admin_user']['username'] == WEBADMIN) {
						?>
						<div class="form-group row">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right">後台權限</label>							
							<div class="col-sm-9 form-inline">
								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-left">名單管理</label>
								<select class="selectpicker form-control col-sm-10  "  
								data-none-selected-text="請選擇"  size="1"  name="opt1[]"   multiple  ><?php echo $opt1 ?></select>
							</div>
							
							<div class="col-sm-9 offset-2 form-inline select-mar">
								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-left">門禁管理</label>	
								<select class="selectpicker form-control col-sm-10  "  
								data-none-selected-text="請選擇"  size="1"  name="opt2[]"   multiple  ><?php echo $opt2 ?></select>
							</div>

							<div class="col-sm-9 offset-2 form-inline select-mar">
								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-left ">電力管理</label>	
								<select class="selectpicker form-control col-sm-10  "  
								data-none-selected-text="請選擇" size="1"  name="opt3[]"   multiple  ><?php echo $opt3 ?></select>
							</div>
						</div>
						<?php
						}
						?>
						
							
						

<!-- 後台權限複選  END -->
<!-- 卡片權限複選 -->

						<div class="form-group row label-mar">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right">卡片權限</label>							
							<div class="col-sm-9  form-inline ">
								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-left">宿舍門禁</label>	
								<select class="selectpicker form-control col-sm-10  "  
								data-none-selected-text="請選擇" data-actions-box="true"   
								data-select-all-text='全選' data-deselect-all-text='取消全選' size="1"  name="opt4[]"   multiple  ><?php echo $opt4 ?></select>
							</div>
							<div class="col-sm-9 offset-2  form-inline select-mar">
								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-left">電梯設置</label>	
								<select class="selectpicker form-control col-sm-10  "  
								data-none-selected-text="請選擇"   data-actions-box="true" 
								data-select-all-text='全選' data-deselect-all-text='取消全選'size="1"  name="opt5[]"   multiple  ><?php echo $opt5 ?></select>
							</div>


							<div class="col-sm-9 offset-2  form-inline select-mar">
								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-left">房號</label>	
								<input readonly="readonly" type="text" class="form-control col-sm-10 " placeholder="管理層顯示全部，其餘顯示無" value="<?php echo $opt6 ?>">

							</div>
						</div>						
						

						<br>

						<div class="form-group row">
							<label for="exampleFormControlInput1"  class="col-sm-2 col-form-label label-right">備註</label>	
							<div class="col-sm-9">
								<input type="text"  maxlength='30' class="form-control" id="exampleFormControlInput1" name="grp_remark" value="<?php echo $row['remark'] ?>">
							</div>
						</div>
					  <br><br>
					  <button type="submit" class="btn  btn-loginfont btn-primary2  col-6 offset-3" onclick="return confirm('確認修改?')">確認修改</button>
					  <input type='hidden' name='id' value='<?php echo $row['id'] ?>'>
						<?php						
						foreach($door_arr as $v) {
							echo "<input type='hidden' name='door_id_old[]' value='{$v}'>";
						}
						
						foreach($elevator_arr as $v) {
							echo "<input type='hidden' name='elevator_id_old[]' value='{$v}'>";
						}
						?>
					</form>

<br><br>

<!--卡片權限複選  END-->
				</div>
			</div>
		</div>
	</div>
</section>
<?php // iframe('');?>
<?php include('footer_layout.php'); ?>