<?php

    include('includes/header.php');
    include('includes/nav.php');
	include('includes/scripts.php');
	
 	$pagesize = 10;
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';

	$data1;
	$data2;
	$sql_dt      = "";
	$sql_kw      = "";
	$def_in_use  = "---";
	
 	$cname       = $_GET['cname'];
 	$username    = $_GET['username'];
 	$room_num    = $_GET['room_strings'];
	$dong        = $_GET['dong'];
	$floor       = $_GET['floor'];
	$start_date  = $_GET['start_date'];
	$end_date    = $_GET['end_date'];
	$status		 = $_GET['status'];
	$search      = $_GET['search'];
	
	if($username) { $sql_kw .= " AND `username` LIKE '%".trim($username)."%' "; }
	if($cname)    { $sql_kw .= " AND `cname` LIKE '%".trim($cname)."%' "; }
	if($room_num) { $sql_kw .= " AND  r.name LIKE '%".trim($room_num)."%' "; }
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
				m.username, m.cname, m.id_card, '{$def_in_use}' as `balance`, r.dong, r.floor, r.name, m.berth_number 
				FROM `room_electric_situation` res
				LEFT JOIN `member` m ON m.id = res.member_id 
				LEFT JOIN `room` r ON r.id = res.room_id
				WHERE res.powerstaus = 1 AND r.Title <> '研習室' {$sql_kw}{$sql_dt} ORDER BY start_date DESC";
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
				rer.start_balance, rer.end_balance, m.username, m.cname, m.id_card,
				IFNULL(rer.end_balance, 0) as `balance`, r.dong, r.floor, r.name, m.berth_number 
				FROM `room_electric_record` rer
				LEFT JOIN `member` m ON m.id = rer.member_id 
				LEFT JOIN `room` r ON r.id = rer.room_id
				WHERE 1 AND r.Title <> '研習室' {$sql_kw}{$sql_dt} ORDER BY start_date DESC";
 
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

<!-- Begin Page Content -->
<div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">電力使用紀錄</h1>
        <!--<p class="text-lg text-center font-weight-bold NG-color">
			欄位:開電前度數、開電後度數、使用前金額、使用後金額，要從3張表撈(具體位置找浩軒)。<br>
			可參考北護的「空調用電使用紀錄」處理
		</p>
		-->
		<!-- SEARCH 電力使用紀錄-->
		<div class='col-12'>
				<form id='mform1' method="get">
						<div class='panel-body'>
								<!--
								<div class='form-group row'>
									<label for='exampleFormControlInput1'  class='col-sm-2 col-form-label label-right' >編號</label>
									<div class='col-sm-8 input-group-lg'> 
										<input type='text' class='form-control  col'  maxlength='10'  name='username' placeholder='全部學號/教職員工' value='<?php //echo $username ?>'>  
									</div>
								</div>
								-->


								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'><?php echo $lang->line("index.room_bed_number");?></label>
									<div class='col-sm-8 input-group-lg'> 
										<input type='text' class='form-control' name='room_strings' placeholder='全部' value='<?php echo $room_num ?>'>
									</div>
								</div>

								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right' >姓名</label>
									<div class='col-sm-8 input-group-lg'>
										<input   type='text'  class='form-control' name='cname' placeholder='全部' value='<?php echo $cname ?>'>  
									</div>
								</div>

								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right' >學號</label>
									<div class='col-sm-8 input-group-lg'>
										<input  type='text' maxlength='10' class='form-control  col' name='username' placeholder='全部' value='<?php echo $username ?>'>  
									</div>
								</div>

								
								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right pd-top25'>棟別</label>
									<div class='col-sm-8 form-inline'> 
										<select  class='col form-control selectpicker custom-select-lg' size='1' name='dong'>
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

								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right pd-top25'>樓層</label>
									<div class='col-sm-8 form-inline'> 
										<select  class='col form-control selectpicker custom-select-lg' size='1' name='floor'>
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

								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>開始時間</label>
									<div class='col-sm-8 input-group-lg'> 
										<input  type='date' class='form-control date-pd' name='start_date' value='<?php echo $start_date ?>'>										
									</div>
								</div>

								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>結束時間</label>
									<div class='col-sm-8 input-group-lg'> 
										<input  type='date'  class='form-control date-pd' name='end_date' value='<?php echo $end_date ?>'>
									</div>
								</div>
								
								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right pd-top25'>狀態</label>
									<div class='col-sm-8 form-inline'> 
										<select  class='col form-control selectpicker custom-select-lg' size='1' name='status'>
											<option value='0' <?php echo $status == 0 ? "selected" : "" ?>>全部</option>
											<option value='1' <?php echo $status == 1 ? "selected" : "" ?>>使用中</option>
											<option value='2' <?php echo $status == 2 ? "selected" : "" ?>>結束用電</option>
										</select>
									</div>
								</div>		


									<br>
									<input type='hidden' name='search' value='1'>
									<button type='button' onclick='export_list()' class='btn btnfont-30 btn-primary2 text-white col-sm-4 offset-sm-4'>查詢</button>
						</div>
				</form>
	    </div>
		<br>
		<!-- SEARCH 電力使用紀錄 END-->

		<!--
		<div class=" text-right " style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
				<button type="button" onclick='export_file()' class="btn btnfont-30 text-white btn-primary2 col-sm-2">匯出</button>
		</div>
		<br> -->

        <!--Table--->
        <div class="table-responsive" style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
                      <table class="table  text-center font-weight-bold">
                        <thead class="thead-green">
                        <tr class="text-center">
						  <th scope="col">開始日期 ~ 結束時間</th>
                          <th scope="col"><?php echo $lang->line("index.room_bed_number");?></th>
                          <th scope="col">姓名/卡號/學號</th>
                          <th scope="col">用電度數</th>
                          <th scope="col">開始度數</th>
                          <th scope="col">結束度數</th>
                          <th scope="col">開始餘額</th>
                          <th scope="col">結束餘額</th>
                          <th scope="col">扣款</th>
                        </tr>
                        </thead>
                        <tbody>
						    <?php 
								foreach($data as $row) 
								{
									$powersts  = $row["power_status"];
									$usage_amt = '';
									$usage_fee = '';
									
									$start_balance = round($row['start_balance'], 1);
									
									if($powersts == 1) {
										$usage_amt   = $def_in_use;
										$usage_fee   = $def_in_use;
										$end_amount  = $def_in_use;
										$end_balance = $def_in_use;
									} else {
										$usage_amt   = round($row['end_amount'] - $row['start_amount'], 2);
										$usage_fee   = round($row['end_balance'] - $row['start_balance'], 1);
										$end_amount  = $row['end_amount'];
										$end_balance = round($row['end_balance'], 1);
									}
							?>
								<tr>
									<td><?php echo $row["start_date"] ?> ~ <?php echo $row["end_date"] ?></td>
									<td><?php echo $row["name"].'/'.$row["berth_number"] ?></td>
									<td><?php echo $row['cname'] ?>/<?php echo $row['id_card'] ?>/<?php echo $row['username'] ?></td>
									<td><?php echo $usage_amt ?></td>
									<td><?php echo $row['start_amount'] ?></td>
									<td><?php echo $end_amount ?></td>
									<td><?php echo $start_balance ?></td>
									<td><?php echo $end_balance ?></td> 
									<td><?php echo $usage_fee ?></td>
							    </tr>
							<?php
								}
						    ?>          
                          </tbody>
                      </table>
					
		</div>
		<div class="row" style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
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


</div>
<!-- /.container-fluid -->

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

<?php  
    include('includes/footer.php');
?>