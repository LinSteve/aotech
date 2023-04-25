<?php 

	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');
	
	$id  = $_GET['id'];
	$sql = "SELECT * FROM `door` WHERE id = '{$id}'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetch();
	
	if($data == '') {
		// header('Location: curfew-editschedule.php?error=2');
		echo "<script>location.replace('curfew-editschedule.php?error=2')</script>";
		exit;
	}
	
	$row_id = $data['id'];
	$mode   = $data['mode'];
	$sched  = $data['schedule_id'];
	
	$sql = 'SELECT * FROM `schedule`';
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$sche_arr = $rs->fetchAll();

	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'door_mode'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$door_mode_arr = $rs->fetchAll();
?>
<!-- 教官查詢房號  -->  

<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='curfew-bindingschedule.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">門禁排程編輯：<?php echo $data['name'] ?></h1>
    </div>
	
	<div class="row container-fluid mar-bot50">
	<?php if($_GET['success'] == 1){ ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-success col-lg-9" role="alert">
		  <strong>更新成功</strong>
		</div>
	<?php } elseif ($_GET['error'] == 1) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
		  <strong>更新失敗</strong>
		</div>
	<?php } elseif ($_GET['error'] == 2) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
		  <strong>沒有可編輯的資料</strong>！！
		</div>
	<?php } ?>
	</div>
	
	<div class="inner">
	<!--編輯-->
	<form id='mform1' action="model/door_upd.php" method="post" class='col-12'>
	<div class="row">
				
				
					<div class='col-12'>
							 
							 <div class='form-group row '>
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>門禁名稱</label>
							 <div class='col-sm-9'> 
								
								 <input  type='text' maxlength='20' class='form-control' name='door_name' value='<?php echo $data['name'] ?>'>
							 </div>
							 </div>

							 <div class='form-group row '>
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>位置</label>
							 <div class='col-sm-9'> 
								 <input  type='text' maxlength='20' class='form-control' name='floor' value='<?php echo $data['floor'] ?>'>
							 </div>
						 	</div>



							<div class="form-group row">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">模式</label>	
							<div class="col-sm-9  form-inline">
								<select class="room_changes col form-control  selectpicker show-tick" title='請選擇'  size="1" name="mode" id="mode">
								<?php
									foreach($door_mode_arr as $v) {
										$opt_key = $v['custom_id'];
										$opt_val = $v['custom_var'];
										$select  = ($mode == $opt_key) ? 'selected' : '';
										echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
									}
								?>
								</select>
							</div>
							</div>							
							 
							<div class="form-group row mar-top30 for_schedule">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">使用排程</label>	
							<div class="col-sm-9  form-inline">
								<select class="room_changes col form-control  selectpicker show-tick " title='請選擇'  size="1" name="schedule" id="schedule">
								<?php
									echo "<option value=''>請選擇</option>";
									foreach($sche_arr as $k => $v) {
										$opt_key = $v['id'];
										$opt_val = $v['name'];
										$select  = ($sched == $opt_key) ? 'selected' : '';
										echo "<option id='TEST' value='{$opt_key}' {$select}>{$opt_val}</option>";
									}
								?>
								</select>
							</div>
							</div>							
							<br><br>
	             	</div>
				
	</div>
<!-- 排程一覽表 -->
	<div class="row for_schedule">
		<div class="col-12">
			<h1 class="jumbotron-heading text-center" id='schedule_name'>學生宿舍排程一覽</h1>
			<div class="table-responsive"><!-- bootstrap 修復 table 跑版 -->
				<table class="table  text-center font-weight-bold">
				<thead class="thead-green">
				<tr class="text-center">
					<th scope="col">星期</th>
					<th scope="col">開始時間～結束時間</th>					
					<th scope="col">啟用狀態</th>
				</tr>

				</thead>
				<tbody id='databody'></tbody>
				</table>
			</div>
		</div>		
	</div>
<!-- 排程一覽表 END-->
<input type='hidden' name='door_id' value='<?php echo $row_id ?>'>
<button type='submit' class='btn  btn-loginfont btn-primary2  col-4 offset-4 mar-top25'
onclick="return confirm('確認提示\n您確定要更新嗎?');">
確認更新
</button>
</form>

</section>


<script>

$(document).ready(function() {
	
	check_mode();
	combine_data();
	
	$('#schedule').change(function() { combine_data(); });
	$('#mode').change(function() { check_mode(); });

});

function combine_data() {
	
	if($('#schedule').val() != '') {
		$.ajax({
			url: "model/combine_schedule.php",
			data: { id: $('#schedule').val(), },
			// dataType: "html",
			type: 'post',
			success: function(data) {
				$('#databody').html(data);
			}
		});
		$('#schedule_name').html($( "#schedule option:selected" ).text());
	} else {
		$('#databody').html('');
	}
}

function check_mode() {
	
	if($('#mode').val() == '2') {
		$('.for_schedule').show();
	} else {
		$('.for_schedule').hide();
	}
}
</script>

<?php include('footer_layout.php'); ?>