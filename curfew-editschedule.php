<?php 

	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

 	$pagesize = 10;
	
	$sql_kw   = "";
	$url_str  = "";
	$sql_in   = "";
	$s_name   = $_GET['s_name'];
	$serach   = $_GET['serach'];
	
	if($s_name) {
		
		foreach($s_name as $v) {
			$sql_in  .= $v.",";
			$url_str .= "&s_name[]=".$v;
		}
		
		$sql_kw = " AND id IN (".substr($sql_in, 0, -1).") ";
	}
	
	$sql = "SELECT count(*) as 'count' FROM `schedule` WHERE 1 ".$sql_kw;
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
		$pageurl .= "<a href='?page=1&serach={$serach}{$url_str}'>".$lang->line("index.home")."</a> | 
					 <a href='?page={$prepage}&serach={$serach}{$url_str}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}&serach={$serach}{$url_str}'>".$lang->line("index.next_page")."</a> | 
					 <a href='?page={$pagenum}&serach={$serach}{$url_str}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT * FROM `schedule` WHERE 1 ".$sql_kw;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	$sql = "SELECT * FROM `schedule` ";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$sc_data = $rs->fetchAll();
?>
<!-- 教官查詢房號  -->  

<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='curfewsetting.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">排程編輯</h1>
    </div>
	
	<div class="row">
	<?php if($_GET['success'] == 1){ ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong>更新成功</strong>
		</div>
	<?php } elseif ($_GET['error'] == 1) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
		  <strong>更新失敗</strong>
		</div>
	<?php } elseif ($_GET['error'] == 2) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
		  <strong>沒有可編輯的資料</strong>！！
		</div>
	<?php } ?>
	</div>
	
	<div class="inner">
		<div class="row">
			<!-- 排程編輯查詢 -->


				<form id='mform1' action="" method="get" class='col-12'>

						<div class='col-12'>
							<section class='panel panel-noshadow'>
								<!--
								<div class='form-group row select-mar'>
							 		<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>編號</label>
							 	<div class='col-sm-9'> 
									<input  type='text' required="required" maxlength="10" class='form-control date-pd' name='' value=''>
							 	</div>
								</div>
 								-->
								<div class="form-group row ">
								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">排程名稱</label>	
								<div class="col-sm-9  form-inline">
									<select class="room_changes col form-control  selectpicker show-tick" title='全部'  size="1" name="s_name[]" multiple>
									<?php
										foreach($sc_data as $v) {
											
											$opt_id   = $v['id'];
											$opt_name = $v['name'];
											$select   = in_array($opt_id, $s_name) ? 'selected' : '';
											echo "<option value='{$opt_id}' {$select}>{$opt_name}</option>";
										}
									?>
									</select>
								</div>
								</div>			
<!--
								<div class="form-group row select-mar">年末設定
								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">門名稱</label>	
								<div class="col-sm-9  form-inline">
									<select class="room_changes col form-control  selectpicker show-tick" title='請選擇'  size="1" name="" multiple >
										<option value='D1東門'>D1東門</option>
										<option value='D1西門'>D2西門</option>

									</select>
								</div>
								</div>							
							 							 

								<div class="form-group row">
								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">選擇排程</label>	
								<div class="col-sm-9  form-inline">
									<select class="room_changes col form-control  selectpicker show-tick" title='請選擇'  size="1" name=""  >
										<option value='學生宿舍'>學生宿舍</option>
										<option value='年末設定'>年末設定</option>

									</select>
								</div>
								</div>							
-->
 <!--
						 	<div class='form-group row select-mar'>
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>開始時間</label>
							 <div class='col-sm-9'> 
								 <input  type='date' class='form-control date-pd' name='room_strings' value='<?php echo $room_num ?>'>
							 </div>
							</div>
							 

							<div class='form-group row'>
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>結束時間</label>
							 <div class='col-sm-9'> 
								 <input  type='date'  class='form-control date-pd' name='room_strings' value='<?php echo $room_num ?>'>
							 </div>
							</div>
-->							 

  
								<br><br>
								<input type='hidden' name='serach' value='1'>
								<button type='submit' class='btn  btn-loginfont btn-primary2  col-4 offset-4'>查詢</button>
	             			</section>
	             		</div>
				</form>
		</div>
	</div>
<!-- 排程編輯查詢 END-->


<!--表格 -->
<div class='inner3' style="display:<?php echo ($serach != '') ? 'block' : 'none'?>">
	<div class="col-12">
				<h1 class="jumbotron-heading text-center h1-mar"><?php echo ($serach != '') ? $lang->line("serach_results") : "" ?></h1>
					<br>
				  	<div class="table-responsive"><!-- bootstrap 修復 table 跑版 -->
						<table class="table  text-center font-weight-bold">
						  <thead class="thead-green">

								<tr class="text-center">
									<th scope="col">編號</th>
									<th scope="col">排程名稱</th>
									<th scope="col">星期一</th>
									<th scope="col">星期二</th>
									<th scope="col">星期三</th>
									<th scope="col">星期四</th>
									<th scope="col">星期五</th>
									<th scope="col">星期六</th>
									<th scope="col">星期日</th>
									<th scope="col">備註</th>
									<th scope="col">操作</th> 
								</tr>
 
						  </thead>
						  <tbody>
						    <?php 
								foreach($data as $row) 
								{
									$row_count = ($prepage * $pagesize) + ++$j;
									$row_id    = $row['id'];
									$row_name  = $row['name'];
									$remark    = $row['remark'];
									
									$sql = "SELECT * FROM `schedule_flow` WHERE `schedule_id` = '{$row_id}' ORDER BY day";
									$rs  = $PDOLink->prepare($sql);
									$rs->execute();
									$flow     = array();
									$flow_arr = $rs->fetchAll();
									
									foreach($flow_arr as $v) {
										$week = $v['day'];
										$flow[$week] = $v;
										if($week == 0) { $flow[7] = $v; }
									}
							?>
								<tr>
									<td scope='row'><?php echo $row_count ?></td>
									<td scope='row'><?php echo $row_name ?></td>
							<?php
									for($i=1; $i<8; $i++) {
										$week_data = $flow[$i];
										
										if(strpos($flow[$i]['time'], '^') !== false) {
											$time_str = "不啟用";
										} else {
											$time_str = str_replace('{', '', str_replace('}', '', $flow[$i]['time']));
										}
							?>
									<td scope="row"><?php echo $time_str ?></td>
							<?php							
									}
							?>
									<td scope='row'><?php echo $remark ?></td>
									<td scope='row'>
										<a href='curfew-editvarious.php?id=<?php echo $row_id ?>' class='btn text-orange  ' title='編輯'><i class='fas fa-pencil-alt'></i></a>
									</td>
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

<?php include('footer_layout.php'); ?>