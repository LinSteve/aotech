<?php

    include('includes/header.php');
    include('includes/nav.php');
	include('includes/scripts.php');
	
 	$pagesize = 10;
	
	$sql_kw      = " AND identity = '0' ";
	$date_format = 'Y/m/d';
	$time_format = 'h:i:s';
	
 	// $cname       = $_GET['cname'];
 	$username    = $_GET['username'];
 	$room_num    = $_GET['room_strings'];
	$search      = $_GET['search'];
	
	if($username) { $sql_kw .= " AND `username` like '%".trim($username)."%' "; }
	if($cname)    { $sql_kw .= " AND `cname` like '%".trim($cname)."%' "; }
	if($room_num) { $sql_kw .= " AND concat(room_strings, berth_number) like '%".trim($room_num)."%' "; }
	if($dong)     { $sql_kw .= " AND `dong` = '{$dong}' "; }
	if($floor)    { $sql_kw .= " AND `floor` = '{$floor}' "; }
	
	$sql = "SELECT count(*) as 'count' FROM `member` WHERE 1 ".$sql_kw;
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
		$pageurl .= "<a href='?page=1&cname={$cname}&username={$username}&room_strings={$room_num}&dong={$dong}&floor={$floor}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.home").
					"</a> | <a href='?page={$prepage}&cname={$cname}&username={$username}&room_strings={$room_num}&dong={$dong}&floor={$floor}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}&cname={$cname}&username={$username}&room_strings={$room_num}&dong={$dong}&floor={$floor}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.next_page").
					"</a> | <a href='?page={$pagenum}&cname={$cname}&username={$username}&room_strings={$room_num}&dong={$dong}&floor={$floor}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT * FROM member WHERE identity = '0' {$sql_kw} ORDER BY update_date DESC LIMIT ".($page-1)* $pagesize . ",".$pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
?>

<!-- Begin Page Content -->
<div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">餘額查詢</h1>
		<div class="row container-fluid mar-bot50 mar-center2">
		<?php if($_GET['success'] == 1){ ?>
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
			<strong><?php echo $lang->line("index.success_room_settings"); ?>!!</strong>
			</div>
		<?php } elseif($_GET['error'] == 1) { ?> 
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
			<strong>Error</strong>資料輸入錯誤或不存在
			</div>	
		<?php } elseif($_GET['error'] == 2) { ?> 
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
			請選擇選項
			</div>					
		<?php } ?>
	</div>    	
        <!--<p class="text-lg text-center font-weight-bold NG-color">
			即北護的餘額查詢：帶出最新餘額，一位學生只會有一筆！
		</p> -->
		
		<!-- SEARCH -->
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
<!--
								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right' >姓名</label>
									<div class='col-sm-8 input-group-lg'>
										<input   type='text'  class='form-control' name='cname' placeholder='全部' value='<?php echo $cname ?>'>  
									</div>
								</div>
-->
								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right' >學號</label>
									<div class='col-sm-8 input-group-lg'>
										<input  type='text' maxlength='10' class='form-control  col' name='username' placeholder='全部' value='<?php echo $username ?>'>  
									</div>
								</div>


									<br>
									<input type='hidden' name='search' value='1'>
									<button type='button' onclick='export_list()' class='btn btnfont-30 btn-primary2 text-white col-sm-4 offset-sm-4'>查詢</button>
						</div>
				</form>
	    </div>
		<br>
		<!-- SEARCH  END-->

		
		<div class=" text-right " style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
				<button type="button" onclick='export_file()' class="btn btnfont-30 text-white btn-primary2 col-sm-2">匯出</button>
		</div>
		<br>

        <!--Table--->
        <div class="table-responsive" style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
                      <table class="table  text-center font-weight-bold">
                        <thead class="thead-green">
                        <tr class="text-center">
						  <th scope="col">最新更新日</th>
                          <th scope="col"><?php echo $lang->line("index.room_bed_number");?></th>
						  <th scope="col">姓名</th>
						  <th scope="col">學號</th>
                          <th scope="col">卡號</th>
                          <th scope="col">餘額</th>
                        </tr>
                        </thead>
                        <tbody>
						    <?php 
								foreach($data as $row) {
									
									$up_date = date($date_format.' '.$time_format, strtotime($row['update_date']));
									$balance = round($row['balance'], 1);
							?>
								<tr>
									<td><?php echo $up_date ?></td>
									<td><?php echo $row["room_strings"]."/".$row["berth_number"] ?></td>
									<td><?php echo $row['cname'] ?></td>
									<td><?php echo $row['username'] ?></td>
									<td><?php echo $row['id_card'] ?></td>
									<td><?php echo $balance ?></td>
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
	
	$('#mform1').prop('action', 'balancesearch.php');
	$('#mform1').prop('method', 'get');
	$('#mform1').submit();
}

function export_file() {

	$('#mform1').prop('action', 'model/member_balance_download.php');
	$('#mform1').prop('method', 'get');
	$('#mform1').submit();
}

</script>

<?php  
    include('includes/footer.php');
?>