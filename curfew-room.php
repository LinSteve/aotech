<?php 
	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

 	$pagesize = 10;
	
	$sql_kw      = "";
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';
	
	$cname       = $_GET['cname'];
	$username    = $_GET['username'];
	$room_num    = $_GET['room_strings'];
	$dong        = $_GET['dong'];
	$floor       = $_GET['floor'];
	$start_date  = $_GET['start_date'];
	$end_date    = $_GET['end_date'];
	$serach      = $_GET['serach'];
	
	if($cname)    { $sql_kw .= " AND m.`cname` like '%{$cname}%' "; }
	if($username) { $sql_kw .= " AND m.`username` like '%{$username}%' "; }
	if($room_num) { $sql_kw .= " AND concat(r.`name`, m.berth_number) like '%{$room_num}%' "; }
	if($dong)     { $sql_kw .= " AND r.`dong` = '{$dong}' "; }
	if($floor)    { $sql_kw .= " AND r.`floor` = '{$floor}' "; }
	
	if($start_date) { 
		$s_time  = date('Y-m-d', strtotime($start_date));
		$sql_kw .= " AND rr.add_date > '{$s_time}' "; 
	}
	
	if($end_date)   { 
		$e_time  = date('Y-m-d', strtotime($end_date." +1 day"));
		$sql_kw .= " AND rr.add_date < '{$e_time}' "; 
	}
	
	$sql = "SELECT count(rr.id) as 'count' FROM `room_access_record` rr 
			LEFT JOIN `room` r ON r.id = rr.room_id
			LEFT JOIN `member` m ON m.id = rr.member_id WHERE 1 ".$sql_kw;
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
		$pageurl .= "<a href='?page=1&cname={$cname}&username={$username}&room_strings={$room_num}&floor={$floor}&dong={$dong}&start_date={$start_date}&end_date={$end_date}&serach={$serach}'>".$lang->line("index.home")."</a> | 
					 <a href='?page={$prepage}&cname={$cname}&username={$username}&room_strings={$room_num}&floor={$floor}&dong={$dong}&start_date={$start_date}&end_date={$end_date}&serach={$serach}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}&cname={$cname}&username={$username}&room_strings={$room_num}&floor={$floor}&dong={$dong}&start_date={$start_date}&end_date={$end_date}&serach={$serach}'>".$lang->line("index.next_page")."</a> | 
					 <a href='?page={$pagenum}&cname={$cname}&username={$username}&room_strings={$room_num}&floor={$floor}&dong={$dong}&start_date={$start_date}&end_date={$end_date}&serach={$serach}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT rr.*, m.cname, m.username, r.dong, r.floor, r.name 
			FROM `room_access_record` rr 
			LEFT JOIN `room` r ON r.id = rr.room_id
			LEFT JOIN `member` m ON m.id = rr.member_id 
			WHERE 1 {$sql_kw} ORDER BY rr.add_date LIMIT ".($page-1) * $pagesize . ",".$pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	$sql = "SELECT dong FROM `room` GROUP BY `dong`";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$dong_arr = $rs->fetchAll();
	
	$sql = "SELECT floor FROM `room` GROUP BY `floor`";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$floor_arr = $rs->fetchAll();
	
	$access_map = array('0' => '進', '1' => '出');
	
	asort($dong_arr);
	asort($floor_arr);
?>
<!-- 教官查詢房號  -->  

<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='curfewsearch.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">房間刷卡明細</h1>
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
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'><?php echo $lang->line("index.room_bed_number");?></label>
							 <div class='col-sm-9'> 
								 <input  type='text' class='form-control' placeholder='全部' name='room_strings' value='<?php echo $room_num ?>'>
							 </div>
						 	</div>

							 <div class='form-group row '>
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>姓名</label>
							 <div class='col-sm-9'> 
								 <input  type='text' maxlength='50' class='form-control' placeholder='全部' name='cname' value='<?php echo $cname ?>'>
							 </div>
							 </div>
							 							 
							 <div class='form-group row '>
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>學號</label>
							 <div class='col-sm-9'> 
								 <input  type='text' maxlength='10' class='form-control' placeholder='全部' name='username' value='<?php echo $username ?>'>
							 </div>
						 	</div>
							
							<div class="form-group row select-mar2">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">棟別</label>	
							<div class="col-sm-9  form-inline">
								<select class="room_changes col form-control  selectpicker show-tick" title='全部'  size="1" name="dong"  >
								<option value=''>全部</option>
								<?php
									foreach($dong_arr as $v) {
										$select = ($v['dong'] == $dong) ? 'selected' : '';
										echo "<option value='{$v['dong']}' {$select}>{$v['dong']}</option>";
									}
								?>
								</select>
							</div>
							</div>							
							 
							<div class="form-group row select-mar4">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">樓層</label>	
							<div class="col-sm-9  form-inline">
								<select class="room_changes col form-control  selectpicker show-tick" title='全部'  size="1" name="floor"  >
								<option value=''>全部</option>
								<?php
									foreach($floor_arr as $v) {
										$select = ($v['floor'] == $floor) ? 'selected' : '';
										echo "<option value='{$v['floor']}' {$select}>{$v['floor']}</option>";
									}
								?>
								</select>
							</div>
							</div>							
							 

 
						 	<div class='form-group row select-mar3'>
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>開始時間</label>
							 <div class='col-sm-9'> 
								 <input  type='date' class='form-control date-pd' name='start_date' value='<?php echo $start_date ?>'>
							 </div>
							</div>
							 

							<div class='form-group row'>
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>結束時間</label>
							 <div class='col-sm-9'> 
								 <input  type='date'  class='form-control date-pd' name='end_date' value='<?php echo $end_date ?>'>
							 </div>
							</div>
							 
							 <div class='form-group row'>
							 <!--僅查詢負值餘額隱藏
							 <div class='col-sm-12 text-center'>
							 		<input id='checkbox3' class='form-contro'  type='checkbox' name='username'>
									<label for='checkbox3' class='col-form-label '>&nbsp;&nbsp;&nbsp;僅查詢負值餘額</label> 
							 </div>
							 -->
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
						<button type="button" onclick='export_group()' class="btn btn-loginfont btn-primary2 col-sm-2 offset-sm-10 mb-3"><?php echo $lang->line("index.export") ?></button>
					</div>
					<br>
				  	<div class="table-responsive"><!-- bootstrap 修復 table 跑版 -->
						<table class="table  text-center font-weight-bold">
						  <thead class="thead-green">
						  <tr class="text-center">
							  <th scope="col">#</th>
							  <th scope="col">刷卡時間</th>
							  <th scope="col">棟別/樓層</th>
							  <th scope="col"><?php echo $lang->line("index.room_bed_number"); ?></th>
							  <th scope="col">學號/<?php echo $lang->line("index.member_name"); ?></th> 
							  <th scope="col">備註</th> 
							</tr>
 
						  </thead>
						  <tbody>
						    <?php 
								foreach($data as $row) 
								{
									$row_count = ($prepage * $pagesize) + ++$j;
									$showtime  = date($date_format.' '.$time_format, strtotime($row['add_date']));
									$showdong  = $row['dong'];
									$showdong .= " / ";
									$showdong .= $row['floor'];
									$showroom  = $row['name'];
									$showroom .= " / ";
									$showroom .= $row['berth_number'];
									$showname  = $row['username'];
									$showname .= " / ";
									$showname .= $row['cname'];
									$show_acs  = $access_map[$row['access']];
							?>
								<tr>
									<td scope='row'><?php echo $row_count ?></td>
									<td scope='row'><?php echo $showtime ?></td> 
									<td scope='row'><?php echo $showdong ?></td>
									<td scope='row'><?php echo $showroom ?></td>
									<td scope='row'><?php echo $showname ?></td>
									<td scope='row'><?php echo $show_acs ?></td>
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
	
	$('#mform1').prop('action', 'curfew-room.php');
	$('#mform1').prop('method', 'get');
	
	$('#mform1').submit();
}

function export_group() {

	$('#mform1').prop('action', 'model/acl_room_download.php');
	$('#mform1').prop('method', 'post');
	
	$('#mform1').submit();
}

</script>

<?php include('footer_layout.php'); ?>