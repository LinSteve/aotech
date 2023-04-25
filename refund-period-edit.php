<?php 
	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');
	
	$id = $_GET['id'];
	
	$sql = "SELECT * FROM refund_interval_setting WHERE id = '{$id}'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetch();

	$week_day = $data['day'];
	
	$schedule = array();
	$sche_tmp = $data['time'];
	$sche_arr = explode('{', $sche_tmp);
	
	foreach($sche_arr as $m) {
		$tmp_arr = explode('}', $m);
		if($tmp_arr[0] != '') {
			$schedule[] = $tmp_arr[0];
		}
	}

	$sql = "SELECT * FROM custom_variables WHERE custom_catgory = 'week' ";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$week_arr = $rs->fetchAll();
	$week_map = array();
	
	foreach($week_arr as $v) {
		$week_map[$v['custom_id']] = $v['custom_var'];
	}
	
	if($week_map[$week_day] != '') {
		$week_title = $week_map[$week_day];
	} else {
		$week_title = $data['day'];
	}
?>
<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='refund-period.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>
	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">	
			設定啟用狀態：<?php echo $week_title ?>
		</h1>
	</div>


	<div class="row  container-fluid mar-bot50">
	<?php if($_GET[success] == 1){ ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong><?php echo $lang->line("index.success_refund_setting"); ?>!!</strong>
		</div>
	<?php } elseif ($_GET[success] == 2) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong><?php echo $lang->line("index.success_room_settings_all"); ?>!!</strong>
		</div>
	<?php } elseif ($_GET[success] == 3) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong>使用時數成功重置!!</strong>
		</div>
	<?php } elseif ($_GET[success] == 4) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong><?php echo $lang->line("index.success_create_member"); ?>!!</strong>
		</div>		
	<?php } elseif ($_GET[success] == 5) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong>使用時數全部成功重置!!</strong> 
		</div>
	<?php } elseif ($_GET[success] == 6) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong>公用卡建立成功!!</strong> 
		</div>
	<?php }elseif ($_GET[error] == 1){ ?> 
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
		  <strong>Error</strong>資料輸入錯誤或不存在
		</div>	
	<?php } ?>
	</div>    


<!--0428TEST-->
<!--
<div class="inner">   
	<div class="row">
		<div class="col-12">
		<div class="card shadow mb-4 ">

			<div class="card-body ">                
					<form id="myForm" action="#" method="get"  >
						
						<div class=" form-group   form-inline">
							<label for="exampleInputPassword1"  class="label-right col-3 offset-1">指定日期</label>
							<input class="form-control col-4 input-lg2" type="date" name="price_start_date" value="">
						</div>
					</form>
			</div>
		</div>
		</div>

	</div>
</div>
-->
<!--TEST END-->

<!--指定日期 -->
<!--編輯時不顯示(先比照舊版)
<div class="inner4">   
	<div class="row">
		<div class="col-12">

                <div class="card-body ">
					<form id="myForm" action="#" method="get"  >
						
						<div class=" form-group   form-inline">
							<label for="exampleInputPassword1"  class="label-center col-2 ">指定日期</label>
							<input class="form-control col-8 input-lg2" type="date" name="price_start_date" value="">
						</div>


					</form>
                </div>

		</div>

	</div>
</div>
-->
<!--指定日期 END-->





<div class="inner inner2">   
	<!--<div class="row">-->
		<form id="myForm" action="model/refund_interval_upd.php" method="post">
			<input type='hidden' name='id' value='<?php echo $id ?>'>
			<input type='hidden' name='weekday' value='<?php echo $week_day ?>'>
<?php

	foreach($schedule as $key => $val) {
		
		$is_disable = '';
		$div_num    = $key + 1;
		
		$start_time = '';
		$end_time   = '';
		
		$tmp_str    = str_replace('^', '', $val);
		$time_arr   = explode('~', $tmp_str);
		
		$start_time = $time_arr[0];
		$end_time   = $time_arr[1];
		
		if(strpos($val, '^') > -1) {
			$is_disable = " checked='checked' ";
		} else {	
			$is_disable = '';
		}
?>
		<div class="col-lg-12">
			<div class="card shadow mb-4">

                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-center">排程<?php echo $div_num ?></h6>
                </div>
                <div class="card-body ">
				
						<div class=" form-group  btn-martop30 form-inline">
							
							<div class="col-sm-4 form-inline">
							<label for="exampleInputPassword1"  class="label-center  col-sm-4">開始時間</label>
							<input class="form-control col-sm-8 input-lg2" type="time" name="start_time[]" placeholder="hrs:mins" value="<?php echo $start_time ?>">
							</div>

							<div class="col-sm-4 form-inline mtb-1">
							<label for="exampleInputPassword1"  class="label-center col-sm-4">結束時間</label>
							<input class="form-control  input-lg2 col-sm-8" type="time" name="end_time[]" placeholder="hrs:mins" value="<?php echo $end_time ?>">
							</div>

							<div class=" col-sm-4 form-inline">
							<input id='checkbox<?php echo $div_num ?>' class='form-contro col-sm-4 '  type='checkbox' name='enable[]' value='<?php echo $key ?>' <?php echo $is_disable ?>>
							<label for='checkbox<?php echo $div_num ?>' class=' col-sm-8 col-form-label '>&nbsp;&nbsp;不啟用</label>
							</div>

						</div>

                </div>

			</div>
		</div>
<?php
	}
?>
		
		<!-- 排程2 

		<div class="col-lg-12">
			<div class="card shadow mb-4">

                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-center">排程2</h6>
                </div>
                <div class="card-body ">

					<form id="myForm" action="#" method="get">
						
						<div class=" form-group  btn-martop30 form-inline">

							<div class="col-4 form-inline">
							<label for="exampleInputPassword1"  class="label-center  col-4">開始時間</label>
							<input class="form-control col-8 input-lg2" type="time" name="one_time_c" placeholder="hrs:mins" value="06:00" >
							</div>

							<div class="col-4 form-inline">
							<label for="exampleInputPassword1"  class="label-center col-4">結束時間</label>
							<input class="form-control  input-lg2 col-8" type="time" name="one_time_d" placeholder="hrs:mins" value="11:00">
							</div>

							<div class=" col-4 form-inline">
							<input id='checkbox2' class='form-contro col-4 '  type='checkbox' name='username'>
							<label for='checkbox2' class=' col-8 col-form-label '>&nbsp;&nbsp;不啟用</label>
							</div>

						</div>	

					</form>


                </div>

			</div>
		</div>
		-->
		
		<button type="submit"  class="btn  btn-h-auto text-white btnfont-30  btn-martop20 btn-marbot30 mar-bot50 font-weight-bold  btn-primary2 col-6 offset-3"
		onclick="return confirm('確認更新?')">確認更新
		</button> 
		</form>
	<!--</div>-->
</div>

<!--退費時段設定 END-->




<!--退費時段設定
<div class="inner">   
	<div class="row">
		<div class="col-12">
			<div class="card shadow mb-4 ">

                <div class="card-body ">
					<form id="myForm" action="#" method="get"  >
						
						<div class=" form-group  btn-martop30 form-inline">
							<label for="exampleInputPassword1"  class="label-center col-2 ">指定日期</label>
							<input class="form-control col-8 input-lg2" type="date" name="price_start_date" value="">
						</div>

						<button type="submit"  class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-6 offset-3">確認新增</button> 

					</form>
                </div>

			</div>
		</div>

	</div>
</div>
-->
</section>


<?php include('footer_layout.php'); ?>