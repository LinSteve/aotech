<?php 

	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

	$id  = $_GET['id'];	
	$sql = "SELECT * FROM `schedule` WHERE id = '{$id}'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetch();
	
	if($data == '') {
		// header('Location: curfew-editschedule.php?error=2');
		echo "<script>location.replace('curfew-editschedule.php?error=2')</script>";
		exit;
	}
	
	$row_id = $data['id'];
	$s_name = $data['name'];
	$remark = $data['remark'];
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'week'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$week_arr = $rs->fetchAll();
	$week_map = array();
	
	foreach($week_arr as $v) {
		$week_map[$v['custom_id']] = $v['custom_var'];
	}
?>
<!-- 教官查詢房號  -->  

<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='curfew-editschedule.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">排程編輯：<?php echo $s_name ?></h1>
    </div>
	
	<div class="row container-fluid mar-bot50 mar-center2">
	<?php if($_GET['success'] == 1){ ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-success col-lg-9" role="alert">
		  <strong>更新成功</strong>
		</div>
	<?php } elseif ($_GET['error'] == 1) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger  col-lg-9" role="alert">
		  <strong>更新失敗</strong>
		</div>
	<?php } elseif ($_GET['error'] == 2) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger  col-lg-9" role="alert">
		  <strong>沒有可編輯的資料</strong>！！
		</div>
	<?php } ?>
	</div>

	<div class="inner">
	<!--編輯名稱備註-->
	<form id='mform1' action="model/schedule_upd.php" method="post" class='col-12'>
		<div class="row">
						<div class='col-12'>
							<section class='panel panel-noshadow'>
							
								<div class='form-group row'>
							 		<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>排程名稱</label>
							 	<div class='col-sm-9'> 
									<input  type='text' required="required" maxlength="10" class='form-control date-pd' name='s_name' value='<?php echo $s_name ?>'>
							 	</div>
								</div>

								<div class='form-group row '>
							 		<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>備註</label>
							 	<div class='col-sm-9'> 
									<input  type='text' maxlength="20" class='form-control date-pd' name='remark' value='<?php echo $remark ?>'>
							 	</div>
								</div>
  
	             			</section>
	             		</div>
		</div>
	<!--編輯名稱備註-->

<!--排程編輯表格-->
		<div class="col-12">

						<div class="table-responsive"><!-- bootstrap 修復 table 跑版 -->
							<table class="table  text-center font-weight-bold">
							<thead class="thead-green">
							<tr class="text-center">
								<th scope="col">星期</th>
								<th scope="col">開始時間</th>
			    				<th scope="col">結束設定</th>					
    							<th scope="col">結束時間</th>
								<th scope="col">不啟用</th>
							</tr>
	
							</thead>
							<tbody>
								<?php
									$sql = "SELECT * FROM `schedule_flow` WHERE `schedule_id` = '{$row_id}' ORDER BY day";
									$rs  = $PDOLink->prepare($sql);
									$rs->execute();
									$flow = array();
									$flow_arr = $rs->fetchAll();
									
									foreach($flow_arr as $v) {
										$week = $v['day'];
										$flow[$week] = $v;
										if($week == 0) { $flow[7] = $v; }
									}
									
									for($i=1; $i<8; $i++) 
									{
										$week_num = $flow[$i]['day'];
										$week_str = $week_map[$week_num];
										$time_str = str_replace('{', '', str_replace('}', '', $flow[$i]['time']));
										$time_arr = explode('~', $time_str);
										$s_time_t = $time_arr[0];
										$e_time   = $time_arr[1];
										$chk_enab = strpos($s_time_t, '^') !== false;
										$s_time   = $chk_enab ? str_replace('^', '', $s_time_t) : $s_time_t;
										$select   = $chk_enab ? 'checked' : '';
										$nextday  = $flow[$i]['nextday'];
								?>
									<tr>
										<td scope='row'><?php echo $week_str ?></td>
										<td scope='row'>
											<input type='hidden' name='row_id' value='<?php echo $row_id ?>'>
											<input type='hidden' name='week_num[]' value='<?php echo $week_num ?>'>
											<input class="form-control  input-lg2 text-center" required="required" type="time" name="s_time[]" placeholder="hrs:mins" value="<?php echo $s_time ?>">
										</td>
										<td scope='row'>
											<select class="form-control text-center cur-select" required="required" name="">
													<option <?php echo $nextday == '0' ? 'selected' : '' ?>>當天</option>
													<option <?php echo $nextday == '1' ? 'selected' : '' ?>>隔天</option>
											<select>
										</td> 
										<td scope='row'>
											<input class="form-control  input-lg2 text-center" required="required" type="time" name="e_time[]" placeholder="hrs:mins" value="<?php echo $e_time ?>">
										</td>
										<td scope='row'>
											<div class="form-check">
												<input type="checkbox" class="form-check-input" name="enable[]" value="<?php echo $week_num ?>" id="exampleCheck<?php echo $week_num ?>" <?php echo $select ?>>
												<label class="form-check-label check-table-position" for="exampleCheck<?php echo $week_num ?>"></label><br><br>
											</div>
										</td>
									</tr>
								<?php
									}
								?>
							</tbody>
							</table>
						</div>

						<div class="text-center">
						<button type="submit" class="btn btn-loginfont btn-primary2  btn-h-auto table-mar  col-6"
							onclick="return confirm('確認提示\n您確定要更新嗎?');">
							確認更新
						</button>
						</div>
		</div>
	</form>
	</div>
<!--排程編輯表格 END-->


</section>


<?php include('footer_layout.php'); ?>