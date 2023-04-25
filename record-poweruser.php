<?php
	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

 	$pagesize = 10;
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';
	$def_in_use  = "使用中";

	$data0;
	$data1;
	$data2;

	// 改查詢房間用電 -- 20200601
	$sql = "SELECT id, `name` FROM `room` WHERE `name` = (SELECT room_strings FROM member WHERE id = '{$user_sn}') ";
	$rs  = $PDOLink->query($sql);
	$tmp = $rs->fetch();
	$room_id  = $tmp['id'];
	$room_num = $tmp["name"];
	
	// powerstaus 0 關 1 開
	if($room_id != '') {
	
		$sql = "SELECT res.start_date, '{$def_in_use}' as 'end_date', res.powerstaus as 'power_status',
				res.start_amonut as 'start_amount', '{$def_in_use}' as 'end_amount', '' as start_balance, '' as end_balance,
				m.username, m.cname, m.berth_number, '{$def_in_use}' as `balance`, r.dong, r.floor, r.name as 'room_num'
				FROM `room_electric_situation` res
				LEFT JOIN `member` m ON m.id = res.member_id 
				LEFT JOIN `room` r ON r.id = res.room_id
				WHERE res.powerstaus = 1 AND room_id = '{$room_id}' ORDER BY start_date DESC";
		$rs  = $PDOLink->prepare($sql);
		$rs->execute();
		$data1 = $rs->fetchAll();
	
		$sql = "SELECT rer.start_date, rer.end_date, '0' as 'power_status', 
				rer.start_amount, rer.end_amount, 
				rer.start_balance, rer.end_balance, m.username, m.cname, m.berth_number, 
				IFNULL(rer.end_balance, 0) as `balance`, r.dong, r.floor, r.name as 'room_num' 
				FROM `room_electric_record` rer
				LEFT JOIN `member` m ON m.id = rer.member_id 
				LEFT JOIN `room` r ON r.id = rer.room_id
				WHERE room_id = '{$room_id}' ORDER BY start_date DESC";
		$rs  = $PDOLink->prepare($sql);
		$rs->execute();
		$data2 = $rs->fetchAll();
	}
	
	foreach($data1 as $v) {
		$data0[] = $v;
	}
	
	foreach($data2 as $v) {
		$data0[] = $v;
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
		$pageurl .= "<a href='?page=1'>".$lang->line("index.home")."</a> | <a href='?page={$prepage}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}'>".$lang->line("index.next_page")."</a> | <a href='?page={$pagenum}'>".$lang->line("index.last_page")."</a>";
	}
	
	for($i = $prepage * $pagesize; ($i < $prepage * $pagesize + $pagesize) & ($i < $rownum); $i++) {
		$data[] = $data0[$i];
	}
?>

<!--COPY 電力使用紀錄後端程式-->


<section id="main" class="wrapper" style='margin-bottom:9.2em;'>

	<div class='col-12 btn-back'>
		<a onclick="window.history.back();" href='#' >
			<i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label>
		</a>
	</div>

	<div class="rwd-box"></div><br><br>

	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading">電力使用紀錄</h1>
		<div class="text-green h4  alert alert-green col-lg-4 col-sm-9" style='float:none;'>
			<i class="fa fa-home"></i>
			<span class="">房號</span>
			<span class="offset-sm-2 text-darkgreen"><?php echo $room_num ?></span>
		</div>
    </div>
	


	<!--表格 -->
	<div class='inner inner2'>
		<div class="col-12">
						<br>
						<div class="table-responsive">
							<table class="table  text-center font-weight-bold">
							<thead class="thead-green">
							<tr class="text-center">
								<th scope="col">#</th>
								<th scope="col">開始時間～結束時間</th>
								<th scope="col">開始度數 ~ 結束度數</th>
								<th scope="col">棟別/樓層</th>
								<th scope="col"><?php echo $lang->line("index.room_bed_number");?></th>
								<th scope="col">學號/<?php echo $lang->line("index.member_name"); ?></th> 
								<th scope="col">電費金額</th> 
								<th scope="col">餘額</th>
								


								</tr>
	
							</thead>
							<tbody class='record-poweruser'>
						    <?php 
								foreach($data as $row) 
								{
									$row_count = ($prepage * $pagesize) + ++$j;
									$show_name = $row['username'];
									$show_name.= " / ";
									$show_name.= $row['cname'];
									$show_fee  = ($row["end_balance"] - $row["start_balance"]);
									
									$powersts  = $row["power_status"];

									// $show_dong = $row['dong'];
									// $show_floor = $row['floor'];
									// $room_num = $row['room_num'];
									// $room_num.= "/";
									// $room_num.= $row['berth_number'];
									// $st_amount =$row['start_amount'];
									// $en_amount =$row['end_amount'];
									
									if($powersts == 1) {
										$elec_fee  = $def_in_use;
										$balance   = $def_in_use;
										$col2style = "text_in_use";
										$col4style = "text_in_use";
										$show_date = date($date_format.' '.$time_format, strtotime($row["start_date"]));
										$show_date.= " ~ ";
										$show_date.= "<font class='{$col2style}'>{$def_in_use}</font>";
									} else {
										$elec_fee  = round($row['end_balance'] - $row['start_balance'], 2);
										$balance   = round($row['balance'], 2);
										$col4style = ($balance < 0) ? "text_negative" : "text_normal";
										// $col4style = ($elec_fee < 0) ? "text_negative" : "text_normal";
										$show_date = date($date_format.' '.$time_format, strtotime($row["start_date"]));
										$show_date.= " ~ ";
										$show_date.= date($date_format.' '.$time_format, strtotime($row["end_date"]));
										if($elec_fee  < 0) 
										{
											$elec_fee =  "<font class='text_in_use'>{$elec_fee}</font>";
										}else{
											$elec_fee = $elec_fee ;
										} 
									}
									$show_dong = $row['dong'];
									$show_floor = $row['floor'];
									$room_num = $row['room_num'];
									$room_num.= "/";
									$room_num.= $row['berth_number'];
									$show_amt  = $row["start_amount"];
									$show_amt .= " ~ ";
									$show_amt .= "<span class='{$col4style}'>".$row["end_amount"]."</span>";
							?>
								<tr>
									<td scope='row'><?php echo $row_count ?></td>
									<td scope='row'><?php echo $show_date ?></td>
									<td scope='row'><?php echo $show_amt ?></td>
									<td scope='row'><?php echo $show_dong ?>/<?php echo $show_floor ?></td>
									<td scope='row'><?php echo $room_num ?></td>
									<td scope='row'><?php echo $show_name?></td>
									<td scope='row' class='<?php echo $col4style ?>'><?php echo $elec_fee  ?></td>
									<td scope='row' class='<?php echo $col4style ?>'><?php echo $balance ?></td>
								 
							    </tr>
							<?php
								}
						    ?>							
							</tbody>
							</table>
						</div>

							<!-- 跳頁 上下頁-->
							<div class="row ">
								<div class="container-fluid text-center">
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

<?php include('footer_layout.php'); ?>