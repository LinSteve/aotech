<?php 
	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');
?>
<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='refund-period.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>
	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">新增指定時段</h1>
	</div>


	<div class="row">
	<?php if($_GET[success] == 1){ ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong><?php echo $lang->line("index.success_room_settings"); ?>!!</strong>
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

<form id="myForm" action="model/refund_interval_add.php" method="post">

<div class="inner4">   
        <div class="card-body ">
						
			<div class=" form-group form-inline">
				<label for="exampleInputPassword1" required='required'  class="label-center col-sm-2 ">指定日期</label>
				<input class="form-control col-sm-8 input-lg2" type="date" name="week_date" id="week_date" value="">
			</div>

        </div>
</div>
<!--指定日期 END-->





<div class="inner inner2">   
	
	<!--<div class="row">-->

		<div class="col-lg-12">
			<div class="card shadow mb-4">

                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-center">排程1</h6>
                </div>
                <div class="card-body ">
				 	<!-- 排程1 -->
						
						<div class=" form-group  btn-martop30 form-inline">

							<div class="col-sm-4 form-inline">
							<label for="exampleInputPassword1"  class="label-center  col-sm-4">開始時間</label>
							<input class="form-control col-sm-8 input-lg2" required="required" type="time" name="start_time1" placeholder="hrs:mins" value="06:00" >
							</div>

							<div class="col-sm-4 form-inline mtb-1">
							<label for="exampleInputPassword1"  class="label-center col-sm-4">結束時間</label>
							<input class="form-control  input-lg2 col-sm-8" required="required" type="time" name="end_time1" placeholder="hrs:mins" value="11:00">
							</div>

							<div class=" col-sm-4 form-inline">
							<input id='checkbox1' class='form-contro col-sm-4 '  type='checkbox' name='enable1' value='1'>
							<label for='checkbox1' class=' col-sm-8 col-form-label '>&nbsp;&nbsp;不啟用</label>
							</div>

						</div>	


                </div>

			</div>
		</div>
		
		<!-- 排程2 -->
		
		<div class="col-lg-12">
			<div class="card shadow mb-4">

                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-center">排程2</h6>
                </div>
                <div class="card-body ">

					
						
						<div class=" form-group  btn-martop30 form-inline">

							<div class="col-sm-4 form-inline">
							<label for="exampleInputPassword1"  class="label-center  col-sm-4 ">開始時間</label>
							<input class="form-control col-sm-8 input-lg2" required="required" type="time" name="start_time2" placeholder="hrs:mins" value="06:00" >
							</div>

							<div class="col-sm-4 form-inline mtb-1">
							<label for="exampleInputPassword1"  class="label-center col-sm-4">結束時間</label>
							<input class="form-control  input-lg2 col-sm-8" required="required" type="time" name="end_time2" placeholder="hrs:mins" value="11:00">
							</div>

							<div class=" col-sm-4 form-inline">
							<input id='checkbox2' class='form-contro col-sm-4 '  type='checkbox' name='enable2' value='2'>
							<label for='checkbox2' class=' col-sm-8 col-form-label '>&nbsp;&nbsp;不啟用</label>
							</div>

						</div>	

                </div>

			</div>
		</div>
		<button type="submit"  class="btn  btn-h-auto text-white btnfont-30  btn-martop20 btn-marbot30 mar-bot50 font-weight-bold  btn-primary2 col-6 offset-3">確認新增</button> 
		

	<!--</div>-->
	
</div>
</form>
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
							<input class="form-control col-sm-8 input-lg2" type="date" name="price_start_date" value="">
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
<script>

$(document).ready(function() {
	
	$('#myForm').submit(function() {
		if( $('#week_date').val() == '' ) {
			alert("請輸入指定日期");
			return false;
		}
	});	
	
});

</script>


<?php include('footer_layout.php'); ?>