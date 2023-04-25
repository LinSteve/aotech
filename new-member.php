<?php 
	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'sex'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$sex_arr = $rs->fetchAll();
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'login' ";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$login_arr = $rs->fetchAll();
	
	$sql = "SELECT * FROM `group` WHERE `enable` = 1 ORDER BY `id` ASC ";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$group_arr = $rs->fetchAll();
	
	$list_q = "SELECT * FROM `member` WHERE username != '".WEBADMIN."' AND id = '".$_SESSION['admin_user']['id']."'";
	$list_r = $PDOLink->prepare($list_q);
	$list_r->execute();
	$row    = $list_r->fetch();
	$member_grp = json_decode($row['group_id']);
?>

<section id="main" class="wrapper">
<div class="col-12 btn-back"><a href="roomlist-manager.php" ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>
	
	<div class="rwd-box"></div><br><br>
	<div class="row  container-fluid mar-bot50 mar-center2">
		<?php if($_GET['success'] == 1){ ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-success col-lg-9" role="alert">
			<strong>新增完成</strong> 
			</div>
		<?php } else if($_GET['error'] == 1){ ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong><?php echo $lang->line("index.Incorrect-or-non-existent-data");?></strong> 
			</div>
		<?php } else if($_GET['error'] == 3){ ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>學號或卡號有重覆，請到修改名單資料查詢!! </strong> 
			</div>
		<?php } else if($_GET['error'] == 4){ ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>查無此房號!!</strong> 
			</div>
		<?php } else if($_GET['error'] == 5){ ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>超過房間人數上限!!</strong> 
			</div>		
		<?php } else if($_GET['error'] == 6){ ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>超過管理員群組人數上限!!</strong> 
			</div>
		<?php } else if($_GET['error'] == 9){ ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>此權限無法新增管理員</strong>  
			</div>
		<?php } else if($_GET['error'] == 10){ ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>卡號請勿大於 4294967295 !! </strong>  
			</div>
		<?php } else if($_GET['error'] == 11){ ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>研習室身分請選擇研習卡</strong>  
			</div>			
		<?php } ?> 
	</div>

	<div class="inner">
		<div class="container">
			<div class="row">
				<!--div class="col-12 alert alert-orange fz-18">
					<p>【製作說明】</p>
						<p>使用情境：key入，研習卡時...</p>
						<p>1.防呆：若卡號跟系統中卡號重複，擋下</p>
						<p>2.防呆：輸入非研習室之房號(room表Title='研習室')，擋下</p>
						<p>3.登入身分：選研習卡(identity = 4 )</p>
						<p>4.承上，群組隱藏</p>
						<p>5.注意：研習卡，只能透過「單筆新增」建立</p>
						<p>6.注意：檢查「清空名單」時，不可清空研習卡</p>
				</div-->
				<div class="col-12">
					<h1 class="jumbotron-heading text-center">單筆新增</h1>
					<form action='model/member_add.php' method='get'>
						<div class="form-group row">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right">編號<?php //echo $lang->line("index.room_number");?></label>
    						<div class="col-sm-9"> 
								<input required="required" maxlength='10' type="text" class="form-control  col" name="username" placeholder="學號/教職員工">  
    						</div>
						</div>

  						<div class="form-group row">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right"><?php echo $lang->line("index.member_name");?></label>
    						<div class="col-sm-9">
								  <input required="required" maxlength='40' type="text" class="form-control" name="member_name" placeholder="<?php //echo $lang->line("index.please_enter_name"); ?>">  
    						</div>
						</div>

						<div class="form-group row select-mar2">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">性別</label>	
							<div class="col-sm-9  form-inline">
								<select required class="room_changes col form-control  selectpicker show-tick" title='請選擇'  size="1" name="member_sex"  >
								<?php
									foreach($sex_arr as $v) {
										$opt_key = $v['custom_id'];
										$opt_val = $v['custom_var'];
										$select  = '';
										echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
									}
								?>
								</select>
							</div>
						</div>

						<div class="form-group row select-mar3">
							<label for="exampleFormControlInput1"  class="col-sm-2 col-form-label label-right"><?php echo $lang->line("index.card_ID");?></label>	
							<div class="col-sm-9">
								<input type="text" maxlength='10' class="form-control" id="exampleFormControlInput1" name="id_card" placeholder="">
							</div>
						</div>
						<div class="form-group row">
							<label for="exampleFormControlInput1"  class="col-sm-2 col-form-label label-right">房號<?php //echo $lang->line("index.room_number");?></label>
							<div class="col-sm-9"> 
								<input  maxlength='5' type="text" class="form-control" name="room_strings" placeholder="">
							</div>
						</div>
						<div class="form-group row">
							<label for="exampleFormControlInput1"  class="col-sm-2 col-form-label label-right"><?php echo $lang->line("index.bed_number");?></label>
							<div class="col-sm-9"> 
								<input  maxlength='1' type="text" class="form-control" name="berth_number" placeholder="">
							</div>
						</div>

						<div class="form-group row select-mar2">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">登入身分</label>							
							<div class="col-sm-9 form-inline">
								<select class="selectpicker form-control col"  required
								title="請選擇後台登入身分"   size="1"  name="identity" >
								<?php
									foreach($login_arr as $v) {
										$opt_key = $v['custom_id'];
										$opt_val = $v['custom_var'];
										$select  = '';
										echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
									}
								?> 
								</select>
							</div>
						</div>

						<?php
							if($_SESSION['admin_user']['username'] == WEBADMIN || in_array('1', $member_grp)) {
						?>
						<div class="form-group row div_group select-mar4">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25" >群組</label>

							<div class="col-sm-9  form-inline"> 
								<select  class="form-control selectpicker col "  title="請選擇" size="1" name="member_grp[]"    data-none-selected-text="" multiple>
								<?php
									foreach($group_arr as $k => $v) {
										$opt_key = $v['id'];
										$opt_val = $v['name'];
										$select  = '';
										echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
									}
								?>
								</select>
							</div>
						</div>
						<?php
							}
						?>
						
						<br><br><button type="submit" class="btn  btn-loginfont btn-primary2   col-sm-6 offset-sm-3"><?php echo $lang->line("index.confirm_create");?></button>

					</form>

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