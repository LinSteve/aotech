<?php 

	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

 	$pagesize = 10;
	
	$sql_kw   = "";
	
 	$room_num    = $_GET['room_strings'];
	$dong        = $_GET['dong'];
	$floor       = $_GET['floor'];
	$serach      = $_GET['serach'];
	
	if($room_num) { $sql_kw .= " AND `name` = '".trim($room_num)."' "; }
	if($dong)     { $sql_kw .= " AND `dong` = '{$dong}' "; }
	if($floor)    { $sql_kw .= " AND `floor` = '{$floor}' "; }
	
	$sql = "SELECT count(*) as 'count' FROM `room` WHERE 1 ".$sql_kw;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$tmp = $rs->fetch();
	
	$rownum = $tmp['count'];
	
	if(isset($_GET['page'])) {
		$page = $_GET['page'];  
	} else {
		$page = 1;                                 
	}
	
	$pageurl  = '';
	$pagenum  = (int) ceil($rownum / $pagesize);  
	$prepage  = $page - 1;                        
	$nextpage = $page + 1;

	if($page == 1) {                         
		$pageurl .= " ".$lang->line("index.home")." | ".$lang->line("index.previous_page")." | ";
	} else {
		$pageurl .= "<a href='?page=1&room_num={$room_num}&dong={$dong}&floor={$floor}&serach={$serach}'>".$lang->line("index.home").
					"</a> | <a href='?page={$prepage}&room_num={$room_num}&dong={$dong}&floor={$floor}&serach={$serach}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}&room_num={$room_num}&dong={$dong}&floor={$floor}&serach={$serach}'>".$lang->line("index.next_page").
					"</a> | <a href='?page={$pagenum}&room_num={$room_num}&dong={$dong}&floor={$floor}&serach={$serach}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT * FROM `room` WHERE 1 {$sql_kw} ORDER BY `name` LIMIT ".($page-1)* $pagesize . ",".$pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	$sql = "SELECT dong, floor FROM `room` GROUP BY dong, floor";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$room_arr  = $rs->fetchAll();
	$dong_arr  = array();
	$floor_arr = array();
	
	foreach($room_arr as $v) {
		$dong_arr[$v['dong']]   = $v['dong'];
		$floor_arr[$v['floor']] = $v['floor'];
	}
	
	ksort($floor_arr);
	ksort($dong_arr);
	//棟別
	$sql = "SELECT dong,dong_name FROM `dongname` ";
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
  	$room_arr  = $rs->fetchAll();
	
	foreach($room_arr as $v) {
		$dong_Map[$v['dong']] = $v['dong_name'];
		if(!in_array($v['dong_name'],$dong_Map))
		{
		$dong_Map[$v['dong_name']] = $v['dong'];
		}
	}
?>
<!-- 教官查詢房號  -->  

<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='powersearch.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

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
	
	<div class="inner">
		<div class="row">
			<!--<a href="member2.php"><i class="fas fa-chevron-circle-left fa-3x"></i></a><br>-->
			<!-- SEARCH -->
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
							 							 
							
							<div class="form-group row select-mar2">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">棟別</label>	
							<div class="col-sm-9  form-inline">
								<select class="room_changes col form-control  selectpicker show-tick"  size="1" name="dong"  >
								<option value=''>全部</option>
								<?php
									foreach($dong_arr as $v) {
										$opt_key = $v;
										$opt_val = $v;
										$select = ($opt_key == $dong) ? 'selected' : '';
										//echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
										echo "<option value='{$v}'{$select}>{$dong_Map[$v]}</option>";
									}
								?>
								</select>
							</div>
							</div>							
							 
							<div class="form-group row select-mar4">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">樓層</label>	
							<div class="col-sm-9  form-inline">
								<select class="room_changes col form-control  selectpicker show-tick" size="1" name="floor"  >
								<option value=''>全部</option>
								<?php
									foreach($floor_arr as $v) {
										$opt_key = $v;
										$opt_val = $v;
										$select = ($opt_key == $floor) ? 'selected' : '';
										echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
									}
								?>
								</select>
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

<!--test-->
<!--表格 -->

<div class='inner' style="display:<?php echo ($serach != '') ? 'block' : 'none'?>">
	<div class="col-12">
				<h1 class="jumbotron-heading text-center h1-mar"><?php echo ($serach != '') ? $lang->line("serach_results") : "" ?></h1>
					<div class=" text-right ">
						<button type="button" onclick='export_file()' class="btn btn-loginfont btn-primary2 col-sm-2 offset-sm-10 mb-3"><?php echo $lang->line("index.export") ?></button>
					</div>
					<br>
				  	<div class="table-responsive"><!-- bootstrap 修復 table 跑版 -->
						<table class="table   table-condensed text-center  font-weight-bold">
						  <thead class="thead-green">
						  <tr class="text-center">
							  <th scope="col">房號</th>
							  <th scope="col">目前電表度數(110V)</th>
							  <th scope="col">目前電表度數(220V)</th><!--新増220使用度數資料 -->
							  <th scope="col">狀態</th>
							  </tr>
 
						  </thead>
						  <tbody>
						    <?php 
								foreach($data as $row) 
								{
									$mode       = $row['mode'];
									$status_on  = "<i class='fas fa-circle text-gray' style='color:#24D354'></i><span>&nbsp;開啟</span>";
									$status_off = "<i class='fas fa-circle text-gray'></i><span>&nbsp;關閉</span>";
									$status_str = $mode == 1 ? $status_on : $status_off;
							?>
								<tr>
									<td scope='row'><?php echo $row['name'] ?></td>
									<td scope='row'><?php echo $row['amount'] ?></td> 
									 <td scope='row'><?php echo $row['amount'] ?></td><!--新増220使用度數資料 -->
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
	
	$('#mform1').prop('action', 'power-nowmeter.php');
	$('#mform1').prop('method', 'get');
	
	$('#mform1').submit();
}

function export_file() {

	$('#mform1').prop('action', 'model/power_nowmeter_download.php');
	$('#mform1').prop('method', 'get');
	
	$('#mform1').submit();
}

</script>

<?php include('footer_layout.php'); ?>