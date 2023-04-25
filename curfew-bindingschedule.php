<?php 

	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

 	$pagesize = 10;
	
	$sql_kw   = "";
	$sql_in   = "";
	$url_str  = "";
	
 	$door     = $_GET['door'];
 	$schedule = $_GET['schedule'];
	$serach   = $_GET['serach'];
	
	if($schedule) { 
		$sql_kw .= " AND d.schedule_id = '{$schedule}' "; 
	}
	
	if($door) { 
	
		foreach($door as $v) {
			$sql_in  .= $v.",";
			$url_str .= "&door[]=".$v;
		}
		
		$sql_kw .= " AND d.id IN (".substr($sql_in, 0, -1).") "; 
	}
	
	$sql = "SELECT count(*) as 'count' FROM `door` d
			LEFT JOIN `schedule` s ON s.id = d.schedule_id WHERE 1 ".$sql_kw;
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
		$pageurl .= "<a href='?page=1&schedule={$schedule}{$url_str}&serach={$serach}'>".$lang->line("index.home")."</a> | 
					 <a href='?page={$prepage}&schedule={$schedule}{$url_str}&serach={$serach}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}&schedule={$schedule}{$url_str}&serach={$serach}'>".$lang->line("index.next_page")."</a> | 
					 <a href='?page={$pagenum}&schedule={$schedule}{$url_str}&serach={$serach}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT d.*, s.name as 's_name', s.remark FROM `door` d
			LEFT JOIN `schedule` s ON s.id = d.schedule_id 
			WHERE 1 {$sql_kw} LIMIT ".($page-1)* $pagesize . ",".$pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	$sql = "SELECT * FROM `door`";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$door_arr = $rs->fetchAll();
	
	$sql = 'SELECT * FROM `schedule`';
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$sche_arr = $rs->fetchAll();

	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'door_mode'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$door_mode_arr = $rs->fetchAll();
	
	foreach($door_mode_arr as $v) {
		$door_mode_map[$v['custom_id']] = $v['custom_var'];
	}
?>
<!-- 教官查詢房號  -->  

<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='curfewsetting.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">門禁排程</h1>
    </div>
	
	<div class="row container-fluid mar-bot50">
	<?php if($_GET['success'] == 1){ ?>
		<div style="margin: 0 auto; text-align: center;;" class="alert alert-success col-lg-9" role="alert">
		  <strong>更新成功</strong>
		</div>
	<?php } elseif ($_GET['error'] == 1) { ?>
		<div style="margin: 0 auto; text-align: center;;" class="alert alert-danger col-lg-9" role="alert">
		  <strong>更新失敗</strong>
		</div>
	<?php } elseif ($_GET['error'] == 2) { ?>
		<div style="margin: 0 auto; text-align: center;;" class="alert alert-danger col-lg-9" role="alert">
		  <strong>沒有可編輯的資料</strong>！！
		</div>
	<?php } ?>
	</div>
	
	<div class="inner">
		<div class="row">
			<!-- 設定排程選項 -->


				<form id='mform1' action="" method="get" class='col-12'>

						<div class='col-12'>
							<section class='panel panel-noshadow'>
									<div class="form-group row ">
									<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">門禁名稱</label>	
									<div class="col-sm-9  form-inline">
										<select class="room_changes col form-control  selectpicker show-tick" title='全部'  size="1" name="door[]" multiple >
										<?php
											foreach($door_arr as $v) {
												$opt_key = $v['id'];
												$opt_val = $v['name'];
												$select  = (in_array($opt_key, $door)) ? 'selected' : '';
												echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
											}
										?>
										</select>
									</div>
									</div>							
															

									<div class="form-group row select-mar">
									<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">排程名稱</label>	
									<div class="col-sm-9  form-inline">
										<select class="room_changes col form-control  selectpicker show-tick" size="1" name="schedule"  >
										<option value=''>全部</option>
										<?php
											foreach($sche_arr as $v) {
												$opt_key = $v['id'];
												$opt_val = $v['name'];
												$select  = ($schedule == $opt_key) ? 'selected' : '';
												echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
											}
										?>
										</select>
									</div>
									</div>							

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
							 <!--<div class='form-group row'>
							 僅查詢負值餘額隱藏
							 <div class='col-sm-12 text-center'>
							 		<input id='checkbox3' class='form-contro'  type='checkbox' name='username'>
									<label for='checkbox3' class='col-form-label '>&nbsp;&nbsp;&nbsp;僅查詢負值餘額</label> 
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
<!-- 設定排程選項 END-->


<!--表格 -->

<div class='inner' style="display:<?php echo ($serach != '') ? 'block' : 'none'?>">
	<div class="col-12">
				<h1 class="jumbotron-heading text-center h1-mar">門禁排程一覽</h1>
					<!--
					<div class=" text-right ">
						<button type="button" onclick='export_group()' class="btn btn-loginfont btn-primary2 col-2"><?php echo $lang->line("index.export") ?></button>
					</div>
					-->
					<br>
				  	<div class="table-responsive"><!-- bootstrap 修復 table 跑版 -->
						<table class="table  text-center font-weight-bold">
						  <thead class="thead-green">
						  <tr class="text-center">
							  <th scope="col">門禁編號</th>
							  <th scope="col">門禁名稱</th>
							  <th scope="col">位置</th>
							  <th scope="col">模式</th>
							  <th scope="col">使用排程</th>
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
									$building  = $row['floor'];
									$mode_str  = $door_mode_map[$row['mode']];
							?>
								<tr>
									<td scope='row'><?php echo $row_count ?></td>
									<td scope='row'><?php echo $row['name'] ?></td> 
									<td scope='row'><?php echo $building ?></td>
									<td scope='row'><?php echo $mode_str ?></td>
									<td scope='row'><?php echo $row['s_name'] ?></td>
									<td scope='row'><?php echo $row['remark'] ?></td>
									<td scope='row'>
										<a href='curfew-editbinding.php?id=<?php echo $row_id ?>' class='btn  text-orange' title='編輯'><i class='fas fa-pencil-alt'></i></a>
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