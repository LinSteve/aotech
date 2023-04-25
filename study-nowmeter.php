<?php  
	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');
 
	$sql_kw   = ""; 
 	$room_num    = $_GET['room_strings']; 
	$serach  = $_GET['serach'];
	
	if($room_num) { $sql_kw .= " AND b.`name` = '".trim($room_num)."' "; } 
	$sql = "
		SELECT a.power_status, b.`name`,b.amount 
		FROM room_study_situation a
		INNER JOIN `room` b ON a.room_id=b.id WHERE 1 {$sql_kw} AND b.Title = '研習室' ORDER BY  b.`name`  ";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll(); 
?>

<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='studysearch.php'><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">用電現況</h1>
    </div>
	
	<div class="row container-fluid mar-bot50 mar-center2">
		<?php if($_GET['error'] == 1){ ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>沒有可匯出的資料！！</strong>
			</div>
		<?php } elseif ($_GET['error'] == 2) { ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>Error Error！！</strong>
			</div>
		<?php } elseif ($_GET['success']) { ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-success col-lg-9" role="alert">
			<strong>Success 成功設置！！</strong>
			</div>
		<?php } ?>
	</div>

	<!-- SEARCH -->
	<div class="inner">
		<div class="row">
				<!--div class="alert alert-orange col-12">
					<p>【製作說明】</p>
					<p>1.目的：呈現研習室的用電現況</p>
					<p>2.協助檢查前端串資料庫後端PHP寫法、功能有無問題，有請協助修正</p>
				</div>-->
				<form id='mform1' action="" method="get" class='col-12'>
					<div class='col-12'>
						<section class='panel panel-noshadow'>
	             			<div class='panel-body'>
							 
							 <div class='form-group row '>
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>房號</label>
							 <div class='col-sm-9'> 
								 <input  type='text' maxlength='5' class='form-control' name='room_strings' placeholder='全部' value='<?php echo $room_num ?>'>
							 </div>
						 	</div>
							</div>							
  
							<br><br>
							<input type='hidden' name='serach' value='1'>
							<button type='button' onclick='export_list()' class='btn  btn-loginfont btn-primary2  col-4 offset-4'><?php echo $lang->line("index.confirm_query") ?></button>
	             			</div>
	             		</section>
	             	</div>
				</form>

		</div>
	</div>
	<!-- SEARCH END-->

	<!--表格 -->
	<div class='inner' style="display:<?php echo ($serach != '') ? 'block' : 'none'?>">
		<div class="col-12 mb-4">
				<h1 class="jumbotron-heading text-center h1-mar"><?php echo ($serach != '') ? $lang->line("serach_results") : "" ?></h1>
				<div class=" text-right ">
					<button type="button" onclick='export_file()' class="btn btn-loginfont btn-primary2 col-sm-2 offset-sm-10 mb-3"><?php echo $lang->line("index.export") ?></button>
				</div>
				<br>
				<div class="table-responsive">
					<table class="table table-condensed text-center font-weight-bold">
					<thead class="thead-green">
					<tr class="text-center">
						<th scope="col">房號</th>
						<th scope="col">目前電表度數</th>
						<th scope="col">狀態</th>
						</tr>

					</thead>
					<tbody>
						<?php 
							foreach($data as $row) 
							{
								// $mode       = $row['mode'];
								$power_status  = $row['power_status'];
								$status_on  = "<i class='fas fa-circle text-gray' style='color:#24D354'></i><span>&nbsp;開啟</span>";
								$status_off = "<i class='fas fa-circle text-gray'></i><span>&nbsp;關閉</span>";
								$status_str = $power_status == 1 ? $status_on : $status_off;
						?>
							<tr>
								<td scope='row'><?php echo $row['name'] ?></td>
								<td scope='row'><?php echo $row['amount'] ?></td> 
								<td scope='row'><?php echo $status_str ?></td>
							</tr>
						<?php
							}
						?>
					</tbody>
					</table>
				</div>

				<!-- 跳頁 上下頁-->
				<div class="row ">
					<div class="container-fluid">
						<div class="text-center" id="dataTable_paginate">
						<?php  
						if($rownum > $pagesize) {
							echo $pageurl;
							echo " ".$lang->line("index.current_page")." $page | ".$lang->line("index.a_few_pages")." $pagenum ".$lang->line("index.page")."";
						}
						?> 
						</div>
					</div>
				</div>
				<!-- 跳頁 上下頁 END -->
		</div>
	</div>
	<!--表格 END-->
</section>


<script>

function export_list() {
	
	$('#mform1').prop('action', 'study-nowmeter.php');
	$('#mform1').prop('method', 'get');
	
	$('#mform1').submit();
}

function export_file() {

	$('#mform1').prop('action', 'model/study_nowmeter_download.php');
	$('#mform1').prop('method', 'get');
	
	$('#mform1').submit();
}

</script>

<?php include('footer_layout.php'); ?>