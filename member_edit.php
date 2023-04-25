<?php 

	include('header_layout.php');
	include('nav.php');          
	include('chk_log_in.php');
	
	$list_q = "SELECT * FROM `member` WHERE username != '".WEBADMIN."' AND id = '".$_GET['id']."'";
	$list_r = $PDOLink->prepare($list_q);
	$list_r->execute();
	$row = $list_r->fetch();
	if($row == "") {
		// header('Location: new-editmember.php?error=2');
		echo "<script>location.replace('new-editmember.php?error=2')</script>";
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
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'sex'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$sex_arr = $rs->fetchAll();
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'login'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$login_arr = $rs->fetchAll();
	
	$sql = "SELECT * FROM `group` WHERE `enable` = 1 ORDER BY `id` ASC";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$group_arr = $rs->fetchAll();
?>
<section id="main" class="wrapper">
<div class="col-12 btn-back"><a href="new-editmember.php" ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="row container-fluid mar-bot50 mar-center2">
		<?php if($_GET['success'] == 1){ ?>
			<div style="margin: 0 auto; text-align: center; " class="alert alert-success col-lg-8" role="alert">
			<strong>設定完成!!</strong>
			</div>
		<?php } elseif ($_GET['error'] == 1) { ?>
			<div style="margin: 0 auto; text-align: center; " class="alert alert-danger col-lg-8" role="alert">
			<strong>設定失敗!!</strong>
			</div>
		<?php } elseif ($_GET['error'] == 2) { ?>
			<div style="margin: 0 auto; text-align: center; " class="alert alert-danger col-lg-8" role="alert">
			<strong>卡號重複!!</strong>
			</div>
		<?php } elseif ($_GET['error'] == 3) { ?>
			<div style="margin: 0 auto; text-align: center; " class="alert alert-danger col-lg-8" role="alert">
			<strong>編號重複!!</strong>
			</div>
		<?php } elseif ($_GET['error'] == 4) { ?>
			<div style="margin: 0 auto; text-align: center; " class="alert alert-danger col-lg-8" role="alert">
			<strong>查無此房號!!</strong>
			</div>
		<?php } elseif ($_GET['error'] == 5) { ?>
			<div style="margin: 0 auto; text-align: center; " class="alert alert-danger col-lg-8" role="alert">
			<strong>超過房間人數上限!!</strong>
			</div>
		<?php } elseif ($_GET['error'] == 6) { ?>
			<div style="margin: 0 auto; text-align: center; " class="alert alert-danger col-lg-8" role="alert">
			<strong>超過管理員群組人數上限!!</strong>
			</div>
		<?php } elseif ($_GET['error'] == 7) { ?>
			<div style="margin: 0 auto; text-align: center; " class="alert alert-danger col-lg-8" role="alert">
			<strong>卡號請勿大於 4294967295 !!</strong>
			</div>	
		<?php } elseif ($_GET['error'] == 8) { ?>
			<div style="margin: 0 auto; text-align: center; " class="alert alert-danger col-lg-8" role="alert">
			<strong>研習室請選擇研習卡身分 !!</strong>
			</div>				
		<?php } else if($_GET['error'] == 9){ ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>此權限無法設定群組為管理員</strong>  
			</div>
		<?php } ?>
	</div>
	<div class="inner">
		<div class="container">
			<div class="row">
				<!--div class="col-12 alert alert-orange fz-18">
					<p>【製作說明】</p>
						<p>使用情境：編輯，研習卡時...</p>
						<p>1.防呆：若卡號跟系統中卡號重複，擋下</p>
						<p>2.防呆：輸入非研習室之房號(room表Title='研習室')，擋下</p>
						<p>3.登入身分：選研習卡(identity = 4 )</p>
						<p>4.承上，群組隱藏</p>
				</div-->
				<div class="col-12">
					<h1 class="jumbotron-heading text-center">修改名單資料</h1>
					<form action="model/member_upd.php" method="post" >					  
						<!--NEW 題目-->
						<div class="form-group row">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right">編號</label>
    						<div class="col-sm-9"> 
								<input type="text" class="form-control  col"  required="required"  maxlength='10'  name="username" placeholder="學號/教職員工" value='<?php echo $username ?>'>
    						</div>
						</div>
						<div class="form-group row">
							<label for="exampleFormControlInput1"   class="col-sm-2 col-form-label label-right"><?php echo $lang->line("index.member_name");?></label>
    						<div class="col-sm-9">
								  <input  type="text" required="required"  maxlength='40' class="form-control" name="member_name" value="<?php echo $member_name ?>">
    						</div>
						</div>

						<div class="form-group row select-mar2">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">性別</label>	
							<div class="col-sm-9 form-inline">
								<select required class="selectpicker form-control col" title="請選擇性別"   size="1"  name="member_sex" >
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

						<div class="form-group row select-mar3">
							<label for="exampleFormControlInput1"  class="col-sm-2 col-form-label label-right"><?php echo $lang->line("index.card_ID");?></label>	
							<div class="col-sm-9">
								<input type="text" class="form-control"  maxlength='10'  id="exampleFormControlInput1" name="id_card" value="<?php echo $id_card ?>">
							</div>
						</div>

						<div class="form-group row">
							<label for="exampleFormControlInput1"   class="col-sm-2 col-form-label label-right">房號</label>
							<div class="col-sm-9"> 
								<input maxlength="5" type="text" class="form-control" name="room_strings" value="<?php echo $room_num ?>">
							</div>
						</div>

						<div class="form-group row">
							<label for="exampleFormControlInput1"   class="col-sm-2 col-form-label label-right"><?php echo $lang->line("index.bed_number"); ?></label>
							<div class="col-sm-9"> 
								<input maxlength="1" type="text" class="form-control" name="berth_number" value="<?php echo $berth_number ?>">
							</div>
						</div>						
						
						<div class="form-group row select-mar2">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">登入身分</label>							
							<div class="col-sm-9 form-inline">
								<select class="selectpicker form-control col" required
								title="請選擇登入後台身分"   size="1"  name="identity" >
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

						<div class="form-group row div_group select-mar4">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">群組</label>
							<div class="col-sm-9  form-inline"> 
								<select class="form-control selectpicker col  "  title="請選擇"  size="1" name="member_grp[]"  multiple>
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
						
						<br><br>
						<button type="submit" class="btn  btn-loginfont btn-primary2  col-sm-6 offset-sm-3 " onclick="return confirm('確認更新?')">確認更新</button>
						
						<input type="hidden" name="id" value="<?php echo $_GET[id]; ?>">
						<input type="hidden" name="username_old" value="<?php echo $username ?>">
						<input type="hidden" name="room_strings_old" value="<?php echo $room_num ?>">
						<input type="hidden" name="id_card_old" value="<?php echo $id_card ?>">
						<input type="hidden" name="identity_old" value="<?php echo $identity ?>">
						<?php						
						foreach($member_grp as $v) {
							echo "<input type='hidden' name='member_grp_old[]' value='{$v}'>";
						}
						?>
						
						
						<!--OLD 題目-->
					</form>
						<!--OLD END-->

				</div>
			</div>
		</div>
	</div>
</section>

<script>

$(document).ready(function() {
	
	check_identity();
	
	$('select[name=identity]').change(function() {
		check_identity();
	});
	
});

function check_identity() {
	
	if($('select[name=identity]').val() == '1') {
		$('.div_group').show();
	} else {
		$('.div_group').hide();
	}
}

</script>

<?php include('footer_layout.php'); ?>