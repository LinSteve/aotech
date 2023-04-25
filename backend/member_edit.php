<?php
    include('includes/header.php');
    include('includes/nav.php');
	
	include_once("../config/db.php");
	// include('chk_log_in.php');
	
	$list_q = "SELECT * FROM `member` WHERE username != '".WEBADMIN."' AND id = '".$_GET['id']."'";
	$list_r = $PDOLink->prepare($list_q);
	$list_r->execute();
	$row = $list_r->fetch();
	
	if($row == "") {
		// header('Location: new-editmember.php?error=2');
		echo "<script>location.replace('editmember.php?error=2')</script>";
		return;
	} 
	
	$username     = $row['username'];
	$id_card      = $row['id_card'];
	
	$member_name  = $row['cname'];
	$member_sex   = $row['sex'];
	$member_grp   = json_decode($row['group_id']);
	
	$identity     = $row['identity'];
	
	$room_num     = $row['room_strings'];
	$berth_number = $row['berth_number'];
	$balance      = $row['balance'];
	
	$sql = "SELECT * FROM `group` WHERE `enable` = 1 ORDER BY `id` ASC";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$group_arr = $rs->fetchAll();
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'sex'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$sex_arr = $rs->fetchAll();
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'login'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$login_arr = $rs->fetchAll();
?>

	<!--CDN
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>  
	-->
<!-- Begin Page Content -->
<div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">修改名單資料</h1>
        <p class="text-lg text-center font-weight-bold NG-color">
			
		</p>
		<!--按下更新後的提示窗:等後端串好再解除-->
    	<div class="container-fluid mar-bot25 mar-center2">
			<?php if($_GET['success'] == 1){ ?>
				<div style="margin: 0 auto; text-align: center; " class="alert alert-success" role="alert">
					<strong>設定完成!!</strong>
				</div>
			<?php } elseif ($_GET['error'] == 1) { ?>
				<div style="margin: 0 auto; text-align: center; " class="alert alert-danger" role="alert">
					<strong>設定失敗!!</strong>
				</div>
			<?php } elseif ($_GET['error'] == 2) { ?>
				<div style="margin: 0 auto; text-align: center; " class="alert alert-danger" role="alert">
					<strong>卡號重複!!</strong>
				</div>
			<?php } elseif ($_GET['error'] == 3) { ?>
				<div style="margin: 0 auto; text-align: center; " class="alert alert-danger" role="alert">
					<strong>編號重複!!</strong>
				</div>
			<?php } elseif ($_GET['error'] == 4) { ?>
				<div style="margin: 0 auto; text-align: center; " class="alert alert-danger" role="alert">
					<strong>查無此房號!!</strong>
				</div>
			<?php } elseif ($_GET['error'] == 5) { ?>
				<div style="margin: 0 auto; text-align: center;" class="alert alert-success col-lg-9" role="alert">
					<strong>超過房間人數上限!!</strong>
				</div>
			<?php } elseif ($_GET['error'] == 6) { ?>
				<div style="margin: 0 auto; text-align: center;" class="alert alert-success col-lg-9" role="alert">
					<strong>超過管理員群組人數上限!!</strong>
				</div>
			<?php } elseif ($_GET['error'] == 7) { ?>
				<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-6" role="alert">
				<strong>卡號不可大於4294967295!!</strong>
				</div>	
			<?php } elseif ($_GET['error'] == 8) { ?>
				<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-6" role="alert">
				<strong>研習室身份請選擇研習卡!!</strong>
				</div>					
			<?php } ?>
			</div>

		<!-- 修改名單資料-->
		<div class='col-12'>
				<form action="model/member_upd.php" method="post" >
						<div class='panel-body'>
								<div class='form-group row'>
									<label for='exampleFormControlInput1'  class='col-sm-2 col-form-label label-right' >編號</label>
									<div class='col-sm-8 input-group-lg'> 
										<input type='text' required="required" class='form-control  col'  maxlength='10'  name='username' value='<?php echo $username ?>'>  
									</div>
								</div>

								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right' >姓名</label>
									<div class='col-sm-8 input-group-lg'>
										<input   type='text' required="required" class='form-control'   name='member_name' value='<?php echo $member_name ?>'>  
									</div>
								</div>


								<div class="form-group row">
									<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right">性別</label>	
									<div class="col-sm-8 form-inline">
										<select required  class="col form-control selectpicker custom-select-lg" title="請選擇"   size="1"  name="member_sex" >
										<?php
											foreach($sex_arr as $v) {
												$opt_key = $v['custom_id'];
												$opt_val = $v['custom_var'];
												$select  = ($opt_key == $member_sex) ? 'selected' : '';
												echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
											}
										?>
										</select>
									</div>
								</div>

								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>卡號</label>
									<div class='col-sm-8 input-group-lg'> 
										<input type='text' class='form-control'  maxlength='10' name="id_card"  value='<?php echo $id_card ?>'>
									</div>
								</div>

								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>房號</label>
									<div class='col-sm-8 input-group-lg'> 
										<input type='text' class='form-control'   maxlength='5' name='room_strings' value='<?php echo $room_num ?>'>
									</div>
								</div>

								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'><?php echo $lang->line("index.bed_number"); ?></label>
									<div class='col-sm-8 input-group-lg'> 
										<input type='text' class='form-control'   maxlength='1'  name='berth_number' value='<?php echo $berth_number ?>'>
									</div>
								</div>								

								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>餘額</label>
									<div class='col-sm-8 input-group-lg'> 
										<input type='number' class='form-control' name='balance' value='<?php echo $balance ?>' step='any'>
									</div>
								</div>

								<div class="form-group row">
									<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right">群組</label>
									<div class="col-sm-8  form-inline"> 
										<select class="form-control selectpicker col  custom-select-lg"  title="請選擇"  size="1" name="member_grp[]" multiple="multiple">
										<?php
											foreach($group_arr as $k => $v) {
												$opt_key = $v['id'];
												$opt_val = $v['name'];
												$select  = (in_array($opt_key, $member_grp)) ? 'selected' : '';
												echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
											}
										?>
										</select>
									</div>
								</div>



								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right pd-top25'>登入身分</label>
									<div class='col-sm-8 form-inline'> 
										<select  class='col form-control selectpicker custom-select-lg' title="請選擇" size='1' name='identity'>
										<?php
											foreach($login_arr as $v) {
												$opt_key = $v['custom_id'];
												$opt_val = $v['custom_var'];
												$select  = ($opt_key == $identity) ? 'selected' : '';
												echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
											}
										?>
										</select>
									</div>
								</div>

								<br>
								<button type='submit' class='btn btnfont-30 btn-primary2 text-white col-sm-4 offset-sm-4' onclick="return confirm('確認更新?')">確認更新</button>
								<input type='hidden' name='serach' value='1'>
								<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
								<input type="hidden" name="username_old" value="<?php echo $username ?>">
								<input type="hidden" name="room_strings_old" value="<?php echo $room_num ?>">
								<input type="hidden" name="id_card_old" value="<?php echo $id_card ?>">
								<input type="hidden" name="identity_old" value="<?php echo $identity ?>">
								<input type="hidden" name="balance_old" value="<?php echo $balance ?>">
								<?php						
								foreach($member_grp as $v) {
									echo "<input type='hidden' name='member_grp_old[]' value='{$v}'>";
								}
								?>
						</div>
				</form>
	    </div>
		<br>
		<!-- 修改名單資料 END-->


</div>
<!-- /.container-fluid -->


<?php
    include('includes/scripts.php');
    include('includes/footer.php');
?>