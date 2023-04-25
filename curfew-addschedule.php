<?php 
	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');
?>
<!-- 教官查詢房號  -->  

<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='curfewsetting.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">新增排程</h1>
    </div>
	
	<div class="row">
	<?php if($_GET['success'] == 1) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong>新增完成</strong>
		</div>
	<?php } elseif ($_GET['error'] == 1) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
		  <strong>新增失敗</strong>
		</div>
	<?php } ?>
	</div>
	
	<div class="inner">
		<div class="row">
			<!-- 新增排程選項 -->


				<form id='mform1' action="model/schedule_add.php" method="post" class='col-12'>

						<div class='col-12'>
							<section class='panel panel-noshadow'>


								<div class='form-group row'>
							 		<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>排程名稱</label>
							 	<div class='col-sm-9'> 
									<input  type='text' required="required" maxlength="10" class='form-control date-pd' name='s_name' value=''>
							 	</div>
								</div>


								<div class='form-group row'>
								<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>開始時間</label>
								<div class='col-sm-9'> 
									<input  type='time' class='form-control time-height2'  name="s_time" placeholder="hrs:mins" value="00:00">
								</div>
								</div>
								

								<div class='form-group row'>
								<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>結束時間</label>
								<div class='col-sm-9'> 
									<input  type='time'  class='form-control time-height2'  name="e_time" placeholder="hrs:mins" value="05:00">
								</div>
								</div>


								<div class='form-group row '>
							 		<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>備註</label>
							 	<div class='col-sm-9'> 
									<input  type='text' maxlength="10" class='form-control date-pd' name='remark' value=''>
							 	</div>
								</div>
  
								<br><br>
								<button type='submit' class='btn  btn-loginfont btn-primary2  col-4 offset-4'>確認新增</button>
	             			</section>
	             		</div>
				</form>
		</div>
	</div>
<!-- 新增排程選項 END-->


</section>

<?php include('footer_layout.php'); ?>