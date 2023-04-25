<?php 
	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

 	$pagesize = 10;
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';

	$data1 = array();
	$data2 = array();
	$data0 = array();
	$data = array();
	$sql_dt      = "";
	$sql_kw      = "";
	$def_in_use  = "使用中";
	
 	$cname       = trim($_GET['cname']);
 	$id_card    = trim($_GET['id_card']);
 	$room_num    = trim($_GET['room_strings']);
 
	$start_date  = $_GET['start_date'];
	$end_date    = $_GET['end_date'];
	$status		 = $_GET['status'];
	$search      = $_GET['search'];
	
	$sql = "SELECT dong, floor FROM `room` WHERE Title = '研習室' GROUP BY dong, floor";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$room_arr  = $rs->fetchAll();
	// $dong_arr  = array();
	// $floor_arr = array();
	
	foreach($room_arr as $v) {
		$dong_arr[$v['dong']]   = $v['dong'];
		$floor_arr[$v['floor']] = $v['floor'];
	}
	
	ksort($floor_arr);
	ksort($dong_arr);

	if($search == '1')
	{	
		$PDOLink -> setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false); // 非緩衝查詢

		// if($username) { $sql_kw .= " AND `username` like '%".trim($username)."%' "; }
		if($id_card) { $sql_kw .= " AND id_card like '%{$id_card}%' "; }
		if($cname)    { $sql_kw .= " AND `cname` like '%{$cname}%' "; }
		if($room_num) { $sql_kw .= " AND r.name LIKE '%{$room_num}%' "; }
		
		if($status != '2')
		{			
			$sql_dt = "";
			
			if(isset($start_date) && !empty($start_date)) 
			{ 
				if($start_date) { 
					$s_time  = date('Y-m-d', strtotime($start_date));
					$sql_dt .= " AND res.start_date > '{$s_time}' "; 
				}
			}
			
			if(isset($end_date) && !empty($end_date)) 
			{ 
				if($end_date)   { 
					$e_time  = date('Y-m-d', strtotime($end_date." +1 day"));
					$sql_dt .= " AND res.start_date < '{$e_time}' "; 
				}
			}

			$sql = "SELECT res.start_date, '{$def_in_use}' as 'end_date', res.power_status ,res.rate, 
			 '{$def_in_use}' as 'end_amount', '' as start_balance, '' as end_balance,
			m.username, m.cname, '{$def_in_use}' as use_price, r.dong, r.floor, r.name ,m.id_card ,
			sec_to_time(TIMESTAMPDIFF(SECOND,res.start_date, now())) AS usetime
			FROM `room_study_situation` res
			LEFT JOIN `member` m ON m.id = res.member_id 
			INNER JOIN `room` r ON r.id = res.room_id
			WHERE res.power_status = 1 {$sql_kw}{$sql_dt} ORDER BY start_date DESC"; // 使用中
			// echo $sql .'<br>'; 
			$rs  = $PDOLink->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));
			$rs->execute();
			$data1 = $rs->fetchAll();			
		} 

		if($status != '1') {
			
			$sql_dt = "";
			
			if(isset($start_date) && !empty($start_date)) 
			{ 
				if($start_date) { 
					$s_time  = date('Y-m-d', strtotime($start_date));
					$sql_dt .= " AND rer.start_date > '{$s_time}' "; 
				}
			}
			
			if(isset($end_date) && !empty($end_date))
			{ 
				if($end_date)   { 
					$e_time  = date('Y-m-d', strtotime($end_date." +1 day"));
					$sql_dt .= " AND rer.start_date < '{$e_time}' "; 
				}
			}
			
			$sql = "
					SELECT rer.start_date, rer.end_date, '0' as 'power_status',  m.username, m.cname, 
					rer.rate AS balance, r.dong, r.floor, r.name, m.berth_number ,m.id_card, 
					 sec_to_time(TIMESTAMPDIFF(SECOND,rer.start_date, rer.end_date)) AS usetime, d.rate,
					hour(sec_to_time(TIMESTAMPDIFF(SECOND,rer.start_date, rer.end_date))) * d.rate AS use_price					 
					FROM `room_study_record` rer
					LEFT JOIN `member` m ON m.id = rer.member_id 
					LEFT JOIN `room` r ON r.id = rer.room_id
				    LEFT JOIN room_study_situation d ON  rer.room_id=d.room_id	
					WHERE 1 AND r.Title = '研習室' {$sql_kw}{$sql_dt} ORDER BY start_date DESC";
			$rs  = $PDOLink->prepare($sql,  array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));
			$rs->execute();
			$data2 = $rs->fetchAll();
		}
				// echo $sql .'<br>'; 
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
			$pageurl .= "<a href='?page=1&cname={$cname}&id_card={$id_card}&room_strings={$room_num}&dong={$dong}&floor={$floor}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.home").
						"</a> | <a href='?page={$prepage}&cname={$cname}&id_card={$id_card}&room_strings={$room_num}&dong={$dong}&floor={$floor}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.previous_page")."</a> | ";
		}

		if($page == $pagenum || $pagenum == 0) {     
			$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
		} else {
			$pageurl .= "<a href='?page={$nextpage}&cname={$cname}&id_card={$id_card}&room_strings={$room_num}&dong={$dong}&floor={$floor}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.next_page").
						"</a> | <a href='?page={$pagenum}&cname={$cname}&id_card={$id_card}&room_strings={$room_num}&dong={$dong}&floor={$floor}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.last_page")."</a>";
		}
		
		for($i = $prepage * $pagesize; ($i < $prepage * $pagesize + $pagesize) & ($i < $rownum); $i++) {
			$data[] = $data0[$i];
		}
		$data0 = null;
}

?>
<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='studysearch.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

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
		<?php } elseif ($_GET['error'] == 3) { ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>超過可匯出筆數極限，請縮短查詢時間範圍！</strong>
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
					<p>1.Table:room_study_record、room_study_situation</p>
					<p>2.目的：呈現研習室的電力使用紀錄</p>
					<p>3.欄位如示意表格所示，並依照資料表進行資料排序</p>
					<p>4.匯出記得同步</p>
					<p>5.使用時間:使用中時，計算出現在時間-開始時間；使用結束後，顯示結束時間-開始時間</p>
				</div>-->
				<form id='mform1' action="" method="get" class='col-12'>
					<div class='col-12'>
						<section class='panel panel-noshadow'>
	             			<div class='panel-body'>
							 
							 <div class='form-group row '>
							 <label class='col-sm-2 col-form-label label-right'><?php echo $lang->line("index.room_number");?></label>
							 <div class='col-sm-9'> 
								 <input  type='text' class='form-control' name='room_strings' placeholder='全部' value='<?php echo $room_num ?>'>
							 </div>
						 	</div>

							<div class='form-group row '>
								<label class='col-sm-2 col-form-label label-right'>姓名</label>
								<div class='col-sm-9'>
									<input   type='text'  class='form-control' name='cname' placeholder='全部' value='<?php echo $cname ?>'>  
								</div>
							</div>
														
							<div class='form-group row '>
							<label  class='col-sm-2 col-form-label label-right'>卡號</label>
							<div class='col-sm-9'> 
								<input  type='text' maxlength='10' class='form-control  col' name='id_card' placeholder='全部' value='<?php echo $id_card ?>'>  
							</div>
							</div>
 
						 	<div class='form-group row'>
							 <label class='col-sm-2 col-form-label label-right'>開始時間</label>
							 <div class='col-sm-9'> 
								 <input  type='date' class='form-control date-pd' name='start_date' value='<?php if($_GET['start_date'] == '') {echo date('Y-m-d'); }else{echo $_GET['start_date'];} ?>'>
							 </div>
							</div>
							 

							<div class='form-group row'>
							 <label class='col-sm-2 col-form-label label-right'>結束時間</label>
							 <div class='col-sm-9'> 
								 <input  type='date'  class='form-control date-pd' name='end_date' value='<?php if($_GET['end_date'] == '') {echo date('Y-m-d'); }else{echo $_GET['end_date'];} ?>'>
							 </div>
							</div>

							<div class="form-group row select-mar2">
								<label class="col-sm-2 col-form-label label-right pd-top25">狀態</label>	
								<div class="col-sm-9  form-inline">
									<select class="room_changes col form-control  selectpicker show-tick" title=''  size="1" name="status"  >
										<option value='0' <?php echo $status == 0 ? "selected" : "" ?>>全部</option>
										<option value='1' <?php echo $status == 1 ? "selected" : "" ?>>使用中</option>
										<option value='2' <?php echo $status == 2 ? "selected" : "" ?>>結束用電</option>
									</select>
								</div>
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

	<!--表格 -->
	<div class='container' style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
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
								<th scope="col">開始時間</th>
								<th scope="col">結束時間</th>
								<th scope="col">使用時間</th>
								<th scope="col">每小時收費</th>
								<th scope="col">房號</th> 
								<th scope="col">卡號/<?php echo $lang->line("index.member_name"); ?></th> 
								<th scope="col">電費金額</th> 
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
											// $balance   = round($row['balance'], 1);
											$balance   =  $row['balance'];
											
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
										
										// $show_amt  = $row["start_amount"];
										$show_amt .= " ~ ";
										$show_amt .= "<span class='{$col4style}'>".$row["end_amount"]."</span>";
										
										$showname  = $row['id_card'];
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
										<td scope='row' class='<?php echo $col3style ?>'><?php echo $row["usetime"] ?></td>
										<td scope='row'><?php echo $row["rate"] ?></td>
										<td scope='row'><?php echo $row["name"] ?></td>
										<td scope='row'><?php echo $showname ?></td>
										<td scope='row' class='<?php echo $col8style ?>'><?php echo $row['use_price']; ?></td> 
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
	
	$('#mform1').prop('action', 'study-record.php');
	$('#mform1').prop('method', 'get');
	$('#mform1').submit();
}

function export_file() {

	$('#mform1').prop('action', 'model/study_record_download.php');
	$('#mform1').prop('method', 'get');
	$('#mform1').submit();
}

</script>

<?php include('footer_layout.php'); ?>