<?php 
	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

 	$pagesize = 10;
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';

	$data1;
	$data2;
	$sql_dt      = "";
	$sql_kw      = "";
	$def_in_use  = "使用中";
	
 	$cname       = $_GET['cname'];
 	$username    = $_GET['username'];
 	$room_num    = $_GET['room_strings'];
	$dong        = $_GET['dong'];
	$floor       = $_GET['floor'];
	$start_date  = $_GET['start_date'];
	$end_date    = $_GET['end_date'];
	$status		 = $_GET['status'];
	$search      = $_GET['search'];
	
	if($username) { $sql_kw .= " AND `username` like '%".trim($username)."%' "; }
	if($cname)    { $sql_kw .= " AND `cname` like '%".trim($cname)."%' "; }
	if($room_num) { $sql_kw .= " AND  concat(r.name, m.berth_number) LIKE '%".trim($room_num)."%' "; }
	if($dong)     { $sql_kw .= " AND `dong` = '{$dong}' "; }
	if($floor)    { $sql_kw .= " AND `floor` = '{$floor}' "; }
	
	if($status != '2') {
		
		$sql_dt = "";
		
		if($start_date) { 
			$s_time  = date('Y-m-d', strtotime($start_date));
			$sql_dt .= " AND res.start_date > '{$s_time}' "; 
		}
		
		if($end_date)   { 
			$e_time  = date('Y-m-d', strtotime($end_date." +1 day"));
			$sql_dt .= " AND res.start_date < '{$e_time}' "; 
		}
		
		$sql = "SELECT res.start_date, '{$def_in_use}' as 'end_date', res.powerstaus as 'power_status',
				res.start_amonut as 'start_amount', '{$def_in_use}' as 'end_amount', '' as start_balance, '' as end_balance,
				m.username, m.cname, '{$def_in_use}' as `balance`, r.dong, r.floor, r.name, m.berth_number 
				FROM `room_electric_situation` res
				LEFT JOIN `member` m ON m.id = res.member_id 
				LEFT JOIN `room` r ON r.id = res.room_id
				WHERE res.powerstaus = 1 AND r.Title<>'研習室' {$sql_kw}{$sql_dt} ORDER BY start_date DESC";
				// echo $sql ;
		$rs  = $PDOLink->prepare($sql);
		$rs->execute();
		$data1 = $rs->fetchAll();
	} 
	
	if($status != '1') {
		
		$sql_dt = "";
		
		if($start_date) { 
			$s_time  = date('Y-m-d', strtotime($start_date));
			$sql_dt .= " AND rer.start_date > '{$s_time}' "; 
		}
		
		if($end_date)   { 
			$e_time  = date('Y-m-d', strtotime($end_date." +1 day"));
			$sql_dt .= " AND rer.start_date < '{$e_time}' "; 
		}
		
		$sql = "SELECT rer.start_date, rer.end_date, '0' as 'power_status', 
				rer.start_amount, rer.end_amount, 
				rer.start_balance, rer.end_balance, m.username, m.cname, 
				IFNULL(rer.end_balance, 0) as `balance`, r.dong, r.floor, r.name, m.berth_number 
				FROM `room_electric_record` rer
				LEFT JOIN `member` m ON m.id = rer.member_id 
				LEFT JOIN `room` r ON r.id = rer.room_id
				WHERE 1 AND r.Title<>'研習室' {$sql_kw}{$sql_dt} ORDER BY start_date DESC";
		$rs  = $PDOLink->prepare($sql);
		$rs->execute();
		$data2 = $rs->fetchAll();
	}
	
	if($status == '1') {
		$data0 = $data1;
	} else if($status == '2') {
		$data0 = $data2;
	} else {
		foreach($data1 as $v) {
			$data0[] = $v;
		}
		
		foreach($data2 as $v) {
			$data0[] = $v;
		}
	}
	
	$rownum = sizeof($data0);
	
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
		$pageurl .= "<a href='?page=1&cname={$cname}&username={$username}&room_strings={$room_num}&dong={$dong}&floor={$floor}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.home").
					"</a> | <a href='?page={$prepage}&cname={$cname}&username={$username}&room_strings={$room_num}&dong={$dong}&floor={$floor}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}&cname={$cname}&username={$username}&room_strings={$room_num}&dong={$dong}&floor={$floor}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.next_page").
					"</a> | <a href='?page={$pagenum}&cname={$cname}&username={$username}&room_strings={$room_num}&dong={$dong}&floor={$floor}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.last_page")."</a>";
	}
	
	for($i = $prepage * $pagesize; ($i < $prepage * $pagesize + $pagesize) & ($i < $rownum); $i++) {
		$data[] = $data0[$i];
	}
	
	$sql = "SELECT dong, floor FROM `room`  WHERE Title<>'研習室' GROUP BY dong, floor";
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
		<h1 class="jumbotron-heading text-center">電力使用紀錄</h1>
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
								 <input  type='text' class='form-control' name='room_strings' placeholder='全部' value='<?php echo $room_num ?>'>
							 </div>
						 	</div>



							 <div class='form-group row '>
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>姓名</label>
							 <div class='col-sm-9'>
								   <input   type='text'  class='form-control' name='cname' placeholder='全部' value='<?php echo $cname ?>'>  
							 </div>
							 </div>
							 							 

							 <div class='form-group row '>
							 <label for='exampleFormControlInput1'  class='col-sm-2 col-form-label label-right'>學號</label>
							 <div class='col-sm-9'> 
								 <input  type='text' maxlength='10' class='form-control  col' name='username' placeholder='全部' value='<?php echo $username ?>'>  
							 </div>
							 </div>
							 

							<div class="form-group row select-mar2">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">棟別</label>	
							<div class="col-sm-9  form-inline">
								<select class="room_changes col form-control  selectpicker show-tick" title='全部'  size="1" name="dong"  >
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

							

							<div class="form-group row select-mar2">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">狀態</label>	
							<div class="col-sm-9  form-inline">
								<select class="room_changes col form-control  selectpicker show-tick" title=''  size="1" name="status"  >
									<option value='0' <?php echo $status == 0 ? "selected" : "" ?>>全部</option>
									<option value='1' <?php echo $status == 1 ? "selected" : "" ?>>使用中</option>
									<option value='2' <?php echo $status == 2 ? "selected" : "" ?>>結束用電</option>
								</select>
							</div>
							</div>	



							 <div class='form-group row'>
							 <!--僅查詢負值餘額隱藏
							 <div class='col-sm-12 text-center'>
							 		<input id='checkbox3' class='form-contro'  type='checkbox' name='negative' value='1' <?php echo $chk_negative ?>>
									<label for='checkbox3' class='col-form-label '>&nbsp;&nbsp;&nbsp;僅查詢負值餘額</label> 
							 </div>
							 -->
						 	</div>
  
							<br><br>
							<input type='hidden' name='search' value='1'>
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

<div class='inner3' style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
	<div class="col-12">
				<h1 class="jumbotron-heading text-center h1-mar"><?php echo ($search != '') ? $lang->line("serach_results") : "" ?></h1>
					<div class=" text-right ">
						<button type="button" onclick='export_file()' class="btn btn-loginfont btn-primary2 col-sm-2 offset-sm-10 mb-3"><?php echo $lang->line("index.export") ?></button>
					</div>
					<br>
				  	<div class="table-responsive"><!-- bootstrap 修復 table 跑版 -->
						<table class="table  text-center font-weight-bold">
						  <thead class="thead-green">
						    <tr class="text-center">
							  <th scope="col">#</th>
							  <th scope="col">開始用電時間</th>
							  <th scope="col">結束用電時間</th>
						      <th scope="col">開始度數 ~ 結束度數(110V)</th> 
							  <th scope="col">開始度數 ~ 結束度數(220V)</th> <!--新増220使用度數資料 -->
							  <th scope="col">棟別/樓層</th>
							  <th scope="col"><?php echo $lang->line("index.room_bed_number");?></th>
							  <th scope="col">學號/<?php echo $lang->line("index.member_name"); ?></th> 
						      <th scope="col">電費金額</th> 
						      <th scope="col">餘額</th>
						    </tr>
						  </thead>
						  <tbody>
						    <?php 
								foreach($data as $row) 
								{
									$row_count = ($prepage * $pagesize) + ++$j;
									$powersts  = $row["power_status"];
									
									if($powersts == 1) {
										$elec_fee  = $def_in_use;
										$balance   = $def_in_use;
										$col3style = "text_in_use";
										$col4style = "text_in_use";
										$col8style = "text_in_use";
										$col9style = "text_in_use";
									} else {
										$elec_fee  = round($row['start_balance'] - $row['end_balance'], 1);
										$balance   = round($row['balance'], 1);
										
										if($row['balance'] < 0) {
											$col3style = "text_normal";
											$col4style = "text_normal";
											$col8style = "text_normal";
											$col9style = "text_negative";
										} else {
											$col3style = "text_normal";
											$col4style = "text_normal";
											$col8style = "text_normal";
											$col9style = "text_normal";
										}
									}
									
									$show_amt  = $row["start_amount"];
									$show_amt .= " ~ ";
									$show_amt .= "<span class='{$col4style}'>".$row["end_amount"]."</span>";
									
									$showname  = $row['username'];
									$showname .= " / ";
									$showname .= $row['cname'];
									
									$building  = $row['dong'];
									$building .= " / ";
									$building .= $row['floor'];
							?>
								<tr>
									<td scope='row'><?php echo $row_count ?></td>
									<td scope='row'><?php echo $row["start_date"] ?></td>
									<td scope='row' class='<?php echo $col3style ?>'><?php echo $row["end_date"] ?></td>
									<td scope='row'><?php echo $show_amt?></td>
									<td scope='row'><?php echo $show_amt?></td><!--新増220使用度數資料 -->
									<td scope='row'><?php echo $building ?></td>
									<td scope='row'><?php echo $row["name"].'/'.$row["berth_number"] ?></td>
									<td scope='row'><?php echo $showname ?></td>
									<td scope='row' class='<?php echo $col8style ?>'><?php echo $elec_fee ?></td> 
									<td scope='row' class='<?php echo $col9style ?>'><?php echo $balance ?></td>
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
	
	$('#mform1').prop('action', 'power-record.php');
	$('#mform1').prop('method', 'get');
	$('#mform1').submit();
}

function export_file() {

	$('#mform1').prop('action', 'model/power_record_download.php');
	$('#mform1').prop('method', 'get');
	$('#mform1').submit();
}

</script>

<?php include('footer_layout.php'); ?>