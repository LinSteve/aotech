<?php

    include('includes/header.php');
    include('includes/nav.php');
	include('includes/scripts.php');
	
 	$pagesize = 10;
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';

	$data1 = array();
	$data2 = array();
	$data0 = array();
	$data = array();
	$sql_dt      = "";
	$sql_kw      = "";
	$def_in_use  = "---"; // 使用中
	
 	$cname       = trim($_GET['cname']);
 	$id_card    = trim($_GET['id_card']);
 	$room_num    = trim($_GET['room_strings']);
	
	// $dong        = $_GET['dong'];
	// $floor       = $_GET['floor'];
	$start_date  = $_GET['start_date'];
	$end_date    = $_GET['end_date'];
	$status		 = $_GET['status']; // 用電狀態
	$search      = $_GET['search'];
	
	if($id_card) { $sql_kw .= " AND m.id_card like '%{$id_card}%' "; }
	if($cname)    { $sql_kw .= " AND m.`cname` like '%{$cname}%' "; }
	if($room_num) { $sql_kw .= " AND r.name LIKE '%{$room_num}%' "; }
	// if($dong) { $sql_kw .= " AND `dong` = '{$dong}' "; }
	// if($floor) { $sql_kw .= " AND `floor` = '{$floor}' "; }
	
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
		$sql = "
		SELECT res.start_date, '---' as 'end_date', res.power_status ,res.rate, m.username, m.cname,  r.dong, r.floor, r.`name` ,m.id_card , 
		sec_to_time(TIMESTAMPDIFF(SECOND,res.start_date, now())) AS usetime, '---' as use_price
		FROM `room_study_situation` res
		LEFT JOIN `member` m ON m.id = res.member_id 
		INNER JOIN `room` r ON r.id = res.room_id
		WHERE res.power_status = 1 {$sql_kw}{$sql_dt} ORDER BY start_date DESC"; // 使用中				
		// echo $sql .'<br>';
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
		$sql = "
			SELECT rer.start_date, rer.end_date, '0' as 'power_status',d.rate, m.username, m.cname, r.dong, r.floor, r.`name`, m.id_card, sec_to_time(TIMESTAMPDIFF(SECOND,rer.start_date, rer.end_date)) AS usetime,  
			hour(sec_to_time(TIMESTAMPDIFF(SECOND,rer.start_date, rer.end_date))) * d.rate AS use_price		 
			FROM `room_study_record` rer
			LEFT JOIN `member` m ON m.id = rer.member_id 
			LEFT JOIN `room` r ON r.id = rer.room_id
			LEFT JOIN room_study_situation d ON  rer.room_id=d.room_id	
			WHERE 1 AND r.Title = '研習室' {$sql_kw}{$sql_dt} ORDER BY start_date DESC";		 
		// echo $sql ;
		$rs  = $PDOLink->prepare($sql,  array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));		
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
		$data1  = null;
		foreach($data2 as $v) {
			$data0[] = $v;
		}
		$data2 = null;
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
		$pageurl .= "<a href='?page=1&cname={$cname}&id_card={$id_card}&room_strings={$room_num}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.home").
					"</a> | <a href='?page={$prepage}&cname={$cname}&id_card={$id_card}&room_strings={$room_num}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}&cname={$cname}&id_card={$id_card}&room_strings={$room_num}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.next_page").
					"</a> | <a href='?page={$pagenum}&cname={$cname}&id_card={$id_card}&room_strings={$room_num}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.last_page")."</a>";
	}
	
	for($i = $prepage * $pagesize; ($i < $prepage * $pagesize + $pagesize) & ($i < $rownum); $i++) {
		$data[] = $data0[$i];
	}
   		$data0 = null; // temp
?>

<!-- Begin Page Content -->
<div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">電力使用紀錄</h1>
		<!-- SEARCH 電力使用紀錄-->
		<div class='col-12'>
				<form id='mform1' method="get">
						<div class='panel-body'>
								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>房號</label>
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
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right' >卡號</label>
									<div class='col-sm-8 input-group-lg'>
										<input  type='text' maxlength='10' class='form-control  col' name='id_card' placeholder='全部' value='<?php echo $id_card ?>'>  
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
                          <th scope="col">房號</th>
                          <th scope="col">姓名/卡號</th>
					      <th scope="col">使用時間</th>
						  <th scope="col">每小時收費</th>
						  <th scope="col">電費金額</th> 
                          <!--th scope="col">用電度數</th>
                          <th scope="col">開始度數</th>
                          <th scope="col">結束度數</th>
                          <th scope="col">開始餘額</th>
                          <th scope="col">結束餘額</th>
                          <th scope="col">扣款</th>-->
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
										// $usage_amt   = round($row['end_amount'] - $row['start_amount'], 2);
										// $usage_fee   = round($row['end_balance'] - $row['start_balance'], 1);
										$end_amount  = $row['end_amount'];
										$end_balance = round($row['end_balance'], 1);
									}
							?>
								<tr>
									<td><?php echo $row["start_date"].'~'.$row["end_date"]; ?></td>
									<td><?php echo $row["name"]; ?></td>
									<td><?php echo $row['cname'].'/'.$row['id_card']; ?>  </td>
									<td scope='row' class='<?php echo $col3style ?>'><?php echo $row["usetime"]; ?></td>
									<td scope='row'><?php echo $row["rate"] ?></td>
									<td scope='row' class='<?php echo $col8style ?>'><?php echo $row['use_price']; ?></td> 
									<!--td><?php// echo $usage_amt ?></td>
									<td><?php //echo $row['start_amount'] ?></td>
									<td><?php// echo $end_amount ?></td>
									<td><?php //echo $start_balance ?></td>
									<td><?php //echo $end_balance ?></td> 
									<td><?php //echo $usage_fee ?></td>-->
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
	
	$('#mform1').prop('action', 'study-record.php');
	$('#mform1').prop('method', 'get');
	$('#mform1').submit();
}

// function export_file() {

	// $('#mform1').prop('action', 'model/power_record_download.php');
	// $('#mform1').prop('method', 'get');
	// $('#mform1').submit();
// }

</script>

<?php  
    include('includes/footer.php');
?>