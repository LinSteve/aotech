<?php

	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

	$pagesize = 10;
	
	if(isset($_GET['page'])) {
		$page = $_GET['page'];  
	} else {
		$page = 1;                                 
	}
	
	$sql = "SELECT count(*) as 'count' FROM `group`";
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
		$pageurl .= "<a href='?page=1'>".$lang->line("index.home")."</a> | <a href='?page={$prepage}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}'>".$lang->line("index.next_page")."</a> | <a href='?page={$pagenum}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT * FROM `group`  WHERE id > 1 ORDER BY `id` Limit ". ($page-1) * $pagesize .",". $pagesize;	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'enable'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$enable_arr = $rs->fetchAll();
	$enable_map = array();
	
	foreach($enable_arr as $v) {
		$enable_map[$v['custom_id']] = $v['custom_var'];
	}
	
	$list_q = "SELECT * FROM `member` WHERE username != '".WEBADMIN."' AND id = '".$_SESSION['admin_user']['id']."'";
	$list_r = $PDOLink->prepare($list_q);
	$list_r->execute();
	$row    = $list_r->fetch();
	$member_grp = json_decode($row['group_id']);
?>
<!-- admin_users.php -->
<section id="main" class="wrapper">
<div class='col-12 btn-back'><a href='roomlist-manager.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="row ">
	</div>
	<!-- RWD修正 --> 
	<!-- <div class="rwd-box"></div><br><br>  -->

	<h1 class=" col-12 jumbotron-heading text-center">名單匯入</h1>

	<div class="row container-fluid mar-bot50 mar-center2">
	<?php if($_GET['success'] == 1){ ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-success col-lg-6" role="alert">
		  <strong>資料成功上傳！ </strong>
		</div>
	<?php } elseif ($_GET['error'] == 1) { ?> 
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-6" role="alert">
		  <strong>匯入失敗!!請確認房間人數</strong>
		</div>
	<?php } elseif ($_GET['success'] == 2) { ?> 
		<div style="margin: 0 auto; text-align: center;" class="alert alert-success col-lg-6" role="alert">
		  <strong>全部搬出完成!!</strong>
		</div>
	<?php } elseif ($_GET['error'] == 2) { ?> 
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-6" role="alert">
		  <strong>全部搬出失敗!!</strong>
		</div>
	<?php } elseif ($_GET['error'] == 4) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-6" role="alert">
		  <strong>房號錯誤!!</strong>
		</div>
	<?php } elseif ($_GET['error'] == 5) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-6" role="alert">
		  <strong>沒有可下載的資料!!</strong>
		</div>
	<?php } elseif ($_GET['error'] == 6) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-6" role="alert">
		  <strong>超過管理員群組人數上限!!</strong>
		</div>
	<?php } elseif ($_GET['error'] == 7) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-6" role="alert">
		  <strong>卡號重複!!</strong>
		</div>
	<?php } elseif ($_GET['error'] == 8) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-6" role="alert">
		  <strong>資料格式有誤!!</strong>
		</div>
	<?php } elseif ($_GET['error'] == 9) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-6" role="alert">
		  <strong>此權限無法匯入管理員</strong>
		</div>
	<?php } elseif ($_GET['error'] == 10) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-6" role="alert">
		  <strong>卡號不可大於4294967295!!</strong>
		</div>	
	<?php } elseif ($_GET['error'] == 11) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-6" role="alert">
		  <strong>研習卡請至單筆新增建立資料!!</strong>
		</div>			
		<?php } elseif ($_GET['error'] == 12) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-6" role="alert">
		  <strong>學生及公用卡,群組必須空白!!</strong>
		</div>			
	<?php } ?>
	</div>

	<div class="inner">
		<div class="row">
			<div class="col-12">
				<form id='mform1' action="model/member_list_downlad.php" method="post"></form>
				<form id='mform3' action="model/member_list_clear.php" method="post"></form>
				<form id='mform2' action="model/group_list_downlad.php" method="post"></form>
				<!--
				<div class="h4 alert alert-orange mar-bot50">
					<span class="text-orange text-center">
						<i class="fas fa-exclamation-circle"></i>
						檔案編碼請存成UTF-8！
					</span> 
				</div>
				-->
				<form action="model/upload_member.php" method="post" enctype='multipart/form-data'>
					<div class="card card-green"> 
					<div class="card-body">	
							  <div class="form-group text-center" id='input-csv'>
							  	<h1>存檔格式：CSV</h1>
								<h1>編碼格式：UTF-8</h1>
								<label for="exampleInputEmail1"> <?php //echo $lang->line("index.all_room_member_import"); ?> </label> 

								<input name="link1" type="file" id="file"  class="form-control card-green-input span-mar inputbtn-style2 col-sm-12"
								id="exampleInputEmail1" aria-describedby="emailHelp">
								
								<br class="hidden-xs">
								<br class="hidden-xs">
								<br class="hidden-xs">


								<h1 class="text-right span-mar csv-download pd-10"><a href="Excel/csv_sample.csv">下載CSV空白檔案</a></h1>

							  </div>
						</div>
					</div>
					<div class="text-center btn-martop20 col-12 offset-lg-3">
						<button type="submit" class=" btn btn-loginfont  btn-primary2 btn-mar2 col-lg-4 offset-lg-1" 
								onclick="return confirm('確認提示\n您確定要匯入嗎?\n(若為同筆資料確認後，會更新舊有的宿舍名單)');" >
								<?php echo $lang->line("index.change_file_up"); ?>
						</button>	
					</div>
				</form>
					<br class="visible-md-block">
<!--TEST
					<div class="card card-green"> 
						<div class="card-body">	
							  <div class="form-group text-center">
								<label for="exampleInputEmail1"> <?php //echo $lang->line("index.all_room_member_import"); ?> </label> 
								<h1>*僅限CSV格式</h1>
								<input name="link1" type="file" id="file"  class="form-control card-green-input span-mar inputbtn-style2" 
								id="exampleInputEmail1" aria-describedby="emailHelp" style="opacity: 0; z-index:10; max-width:50%;">

								<input type="button" id="file" value="選擇檔案" class="inputbtn-style1 shadow" style="height: 31px;   line-height:unset;  ">
								
								<input name="link2" type="file" id="file"  class="form-control card-green-input span-mar" 
								id="exampleInputEmail1" aria-describedby="emailHelp">

								<h1 class="text-right h1-mar csv-download"><a href="Excel/csv_sample.csv">下載CSV空白檔案</a></h1>

							  </div>
						</div>
					</div>
-->
					<div class="text-center btn-mar col-sm-10  offset-sm-1 offset-lg-2">

						<button type="button" class="btn btn-loginfont btn-primary2 btn-mar2 col-lg-3" onclick="export_list()">現有名單匯出</button>
						<button type="button" class="btn btn-loginfont btn-primary2 btn-mar2 col-lg-3" onclick="clear_list()">清空名單</button>						
												
						<!--
						<button type="submit" class="btn btn-loginfont  btn-primary2 btn-mar2" 
								onclick="return confirm('確認提示\n您確定要匯入嗎?\n(若為同筆資料確認後，會更新舊有的宿舍名單)');" >
								<?php //echo $lang->line("index.change_file_up"); ?>
						</button>-->
				
						<?php
						if($_SESSION['admin_user']['username'] == WEBADMIN || in_array('3', $member_grp)) {
						?>
						<!--群組代碼BTN 匯出-->
						 <button type="button" class="btn btn-loginfont  btn-green col-lg-4" onclick="export_group()">
							<i class="fas fa-download  "></i>&nbsp下載&nbsp群組代碼表
						</button>
						<?php
						}
						?>
					</div>
			</div>
		</div>  
	</div>

<!--群組一覽表-->
<div class="inner inner2">						
				<div class="container-fluid table-mar">
					<div class="row ">
						<div class="container-fluid">
							<!-- Nav tabs 標籤-->
							<!--加入COL
							<ul class="nav nav-tabs  nav-border" role="tablist">
												<li class="nav-item col-xl-4 col-md-4"> 

											  </li>
							</ul>-->
											
							<!-- Content Row Tab panes-->
							<?php
							if($_SESSION['admin_user']['username'] == WEBADMIN || in_array('3', $member_grp)) {
							?>
											<div class="row ">
											  <!-- Content Column -->
											  <div class="col-lg-12 mb-4">

												<div class="card shadow mb-4">
													<h1 class=" col-12 jumbotron-heading text-center"></h1>

													<div class="card-body">
														<h1 class=" col-12 jumbotron-heading text-center">名單群組一覽表</h1>

														<div class="tab-content tabcontent-border table-responsive">
															<!--TABLE 日期-->
															<table class="table container-fluid text-center ">
															<thead class="thead-green ">
																<tr class="text-center">
																<th scope="col">群組代碼</th>
																<th scope="col">群組名稱</th>
																<th scope="col">用途說明</th>
																<th scope="col">備註</th>
																<th scope="col">狀態</th>
																</tr>
															</thead>

															<tbody>
															<?php
															
																foreach($data as $k => $v) {
																	echo "<tr><td>{$v['id']}</td>
																		<td>{$v['name']}</td> 
																		<td>{$v['usage']}</td>
																		<td>{$v['remark']}</td>
																		<td>{$enable_map[$v['enable']]}</td></tr>";
																}
																?>
															</tbody>

															</table>
														</div>

														<!--</div>-->
														<div class="row ">
															<div class="container-fluid">
																<div class="dataTables_paginate paging_simple_numbers text-center" id="dataTable_paginate">
																<?php
																	if($rownum > $pagesize){   
																		echo $pageurl;
																		echo " ".$lang->line("index.current_page")." $page | ".$lang->line("index.a_few_pages")." $pagenum ".$lang->line("index.page")."";
																	}
																?>
																</div>
															</div>
														</div>

														<!--查詢選項 END-->
											
													</div>
												</div>
											  </div>
											</div>
							<?php
							}
							?>
						</div><!--container-fluid-->
					</div>
				</div>
			
</div>


<!--群組一覽表-->


</section>

<script>

function export_list() {
	
	$('#mform1').submit();
}

function export_group() {

	$('#mform2').submit();
}

function clear_list() {

	if(confirm("確認提示\n您確定要清空嗎?\n(此動作會清空宿舍名單)")) {
		
		$('#mform3').submit();
	}

	return false;
}
</script>

<?php include('footer_layout.php'); ?>