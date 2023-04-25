<?php 

	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

	$pagesize = 10;
	
	$sql_kw   = " AND del_mark = '0' AND username != '".WEBADMIN."' ";
	$sql_kw   = "";
	
 	$cname    = $_GET['cname'];
 	$username = $_GET['username'];
 	$room_num = $_GET['room_strings'];
	$serach   = $_GET['serach'];
	
	if($username) { $sql_kw .= " AND `username` = '".trim($username)."' "; }
	if($cname)    { $sql_kw .= " AND `cname` = '".trim($cname)."' "; }
	if($room_num) { $sql_kw .= " AND  concat(room_strings, berth_number) LIKE '%".trim($room_num)."%' "; }
	
	if(isset($_GET['page'])) {
		$page = $_GET['page'];  
	} else {
		$page = 1;                                 
	}
	
	$sql = "SELECT count(*) as 'count' FROM `member` WHERE 1 AND balance < 0 ".$sql_kw;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$tmp = $rs->fetch();
	$rownum = $tmp['count'];
	
	$pageurl  = '';
	$pagenum  = (int) ceil($rownum / $pagesize);  
	$prepage  = $page - 1;                        
	$nextpage = $page + 1;
	
	if($page == 1) {                         
		$pageurl .= " ".$lang->line("index.home")." | ".$lang->line("index.previous_page")." | ";
	} else {
		$pageurl .= "<a href='?page=1&cname={$cname}&username={$username}&room_num={$room_num}&serach={$serach}'>".$lang->line("index.home")."</a> | 
					 <a href='?page={$prepage}&cname={$cname}&username={$username}&room_num={$room_num}&serach={$serach}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}&cname={$cname}&username={$username}&room_num={$room_num}&serach={$serach}'>".$lang->line("index.next_page")."</a> | 
					 <a href='?page={$pagenum}&cname={$cname}&username={$username}&room_num={$room_num}&serach={$serach}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT * FROM `member` WHERE 1 AND balance < 0  {$sql_kw} ORDER BY `id` Limit ". ($page-1) * $pagesize .",". $pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
?>
<!-- 教官查詢房號  -->  

<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='powersearch.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">負值餘額查詢</h1>
    </div>

	<div class="row container-fluid mar-bot50 mar-center2">
	<?php if($_GET['error'] == 1){ ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>沒有可匯出的資料！！</strong>
		</div>
	<?php } elseif ($_GET['error'] == 2) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>查無此帳號！！</strong>
		</div>
	<?php } elseif ($_GET['success'] == 1) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-success col-lg-9" role="alert">
		  <strong>更新完成！！</strong>
		</div>
	<?php } elseif ($_GET['success'] == 4) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-success col-lg-9" role="alert">
		  <strong>已刪除！！</strong>
		</div>
	<?php } ?>
	</div>
	
	<div class="inner">
		<div class="row">
			<!--<a href="member2.php"><i class="fas fa-chevron-circle-left fa-3x"></i></a><br>-->
<!-- SEARCH 負值選項-->
					<div class='col-12'>
						<section class='panel panel-noshadow'>					
							<form id='mform1' method="get">

								<div class='panel-body'>
							 	<div class='form-group row '>
							 		<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'><?php echo $lang->line("index.room_bed_number")?></label>
							 		<div class='col-sm-9'> 
								 		<input  type='text' class='form-control' name='room_strings' placeholder='全部' value='<?php echo $room_num ?>'>
							 		</div>
						 		</div>


								 <div class='form-group row'>
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

									<br><br>
									<input type='hidden' name='serach' value='1'>
									<button type='button' onclick='export_list()' class='btn  btn-loginfont btn-primary2  col-4 offset-4'><?php echo $lang->line("index.confirm_query") ?></button>
								</div>
						   </form>
	             		</section>
	             	</div>
					
<!-- SEARCH 負值選項 END-->


<!--查詢結果-->
<div class="col-12" style="display:<?php echo ($serach != '') ? 'block' : 'none'?>">
				<h1 class="jumbotron-heading text-center h1-mar"><?php echo ($serach != '') ? $lang->line("serach_results") : "" ?></h1>
					<div class=" text-right">
						<button type="button" onclick='export_file()' class="btn btn-loginfont btn-primary2 col-sm-2 offset-sm-10 mb-3"><?php echo $lang->line("index.export") ?></button>
					</div>
					<br>

				  <div class="table-responsive"><!-- bootstrap 修復 table 跑版 -->
						<table class="table  text-center">
						  <thead class="thead-green">
							<tr class="text-center">
						      <th scope="col">姓名</th> 
							  <th scope="col">學號</th>
							  <th scope="col"><?php echo $lang->line("index.room_bed_number")?></th>
							  <th scope="col">負值餘額</th>
							</tr>
						  </thead>
						  <tbody>
							<?php 
								foreach($data as $row) {
									$balance   = round($row['balance'], 1);
									$col4style = ($balance < 0) ? "text_negative" : "text_normal";
							?>
									<tr>
										<td scope='row'><?php echo $row['cname'] ?></td>
										<td scope='row'><?php echo $row['username'] ?></td>
										<td scope='row'><?php echo $row['room_strings'].'/'.$row['berth_number'] ?></td>
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
						<div class="container-fluid">
							<div class=" text-center" id="dataTable_paginate">
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
		</div>


		
		<!-- 原頁碼 -->
		</div>
<!--查詢結果 END-->


</section>


<script>


function export_list() {
	
	$('#mform1').prop('action', 'power-debtlist.php');
	$('#mform1').prop('method', 'get');
	
	$('#mform1').submit();
}

function export_file() {

	$('#mform1').prop('action', 'model/debt_list_download.php');
	$('#mform1').prop('method', 'get');
	
	$('#mform1').submit();
}

</script>
<?php include('footer_layout.php'); ?>