<?php 

	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

 	$pagesize = 10;
	
	$sql_kw   = " AND del_mark = '0' AND username != '".WEBADMIN."' ";
	$page_str = "";
	
 	$cname    = $_GET['cname'];
 	$username = $_GET['username'];
 	$room_num = $_GET['room_strings'];
	$resident = $_GET['resident'];
	$group_id = $_GET['member_grp'];
	$serach   = $_GET['serach'];
	
	if($username) { $sql_kw .= " AND `username` = '".trim($username)."' "; }
	if($cname)    { $sql_kw .= " AND `cname` = '".trim($cname)."' "; }
	if($room_num) { $sql_kw .= " AND  concat(room_strings, berth_number) LIKE '%".trim($room_num)."%' "; }
	
	if($group_id) {
		
		foreach($group_id as $v) {
			
			if($v != '') {
				$sql_kw .= " AND group_id REGEXP '[[:<:]]{$v}[[:>:]]' ";
				$page_str .= "&member_grp[]=".$v;
			}
		}
	}
	
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
		$pageurl .= "<a href='?page=1&cname={$cname}&username={$username}&room_num={$room_num}&resident={$resident}&serach={$serach}{$page_str}'>".$lang->line("index.home")."</a> | 
					 <a href='?page={$prepage}&cname={$cname}&username={$username}&room_num={$room_num}&resident={$resident}&serach={$serach}{$page_str}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}&cname={$cname}&username={$username}&room_num={$room_num}&resident={$resident}&serach={$serach}{$page_str}'>".$lang->line("index.next_page")."</a> | 
					 <a href='?page={$pagenum}&cname={$cname}&username={$username}&room_num={$room_num}&resident={$resident}&serach={$serach}{$page_str}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT * FROM `member` WHERE 1 {$sql_kw} LIMIT ".($page-1)* $pagesize . ",".$pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	$sql = "SELECT * FROM `group` WHERE 1 AND `enable` = '1' AND id != '1' ";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$group_arr = $rs->fetchAll();
	$group_map = array();
	
	foreach($group_arr as $v) {
		$group_map[$v['id']] = $v['name'];
	}
	
	$sql = 'SELECT * FROM `room`';
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$room_arr = $rs->fetchAll();
	$room_map = array();
	
	foreach($room_arr as $v) {
		$room_map[$v['name']] = $v['dong']." / ".$v['floor'];
	}
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'sex'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$sex_arr = $rs->fetchAll();
	$sex_map = array();
	
	foreach($sex_arr as $v) {
		$sex_map[$v['custom_id']] = $v['custom_var'];
	}
?>
<!-- 教官查詢房號  -->  

<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='roomgroup.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">群組名單查詢/匯出</h1>
    </div>
	
	<div class="row container-fluid mar-bot50">
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
							 <div class='form-group row form-mar'>
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right pd-top25' >群組名稱</label>
 
							 <div class='col-sm-9 form-inline'> 
								 <select class='form-control selectpicker col' size='1' name='member_grp[]'>
								 <option value=''>全部</option>
								 <?php
									 foreach($group_arr as $k => $v) {
										 
										 $g_id   = $v['id'];
										 $g_name = $v['name'];
										 $select = in_array($g_id, $group_id) ? 'selected' : '';
										 
										 echo "<option value='{$g_id}' {$select}>{$g_name}</option>";
									 }
								 ?>
								 </select>
							 </div>
						 </div>


							 <div class='form-group row select-mar4'>
							 <label for='exampleFormControlInput1'  class='col-sm-2 col-form-label label-right'>編號</label>
							 <div class='col-sm-9'> 
								 <input  type='text' maxlength='10' class='form-control  col' name='username' placeholder='全部卡號/學號/教職員工' value='<?php echo $username ?>'>  
							 </div>
							 </div>
							 
							 <div class='form-group row'>
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>姓名</label>
							 <div class='col-sm-9'>
								   <input   type='text'   class='form-control' name='cname' placeholder='全部' value='<?php echo $cname ?>'>  
							 </div>
						 	</div>
 
						 	<div class='form-group row'>
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'><?php echo $lang->line("index.room_bed_number");?></label>
							 <div class='col-sm-9'> 
								 <input  type='text' class='form-control' placeholder='全部' name='room_strings' value='<?php echo $room_num ?>'>
							 </div>
						 	</div>
  
							<br><br>
							<input type='hidden' name='serach' value='1'>
							<button type='button' onclick='export_list()' class='btn  btn-loginfont btn-primary2  col-4 offset-4'><?php echo $lang->line("index.confirm_query") ?></button>
	             			</div>
	             		</section>
	             	</div>
				</form>
<!-- SEARCH END-->



				<div class="col-12" style="display:<?php echo ($serach != '') ? 'block' : 'none'?>">
				<h1 class="jumbotron-heading text-center h1-mar"><?php echo ($serach != '') ? $lang->line("serach_results") : "" ?></h1>
					<div class="text-right">
						<button type="button" onclick='export_group()' class="btn btn-loginfont btn-primary2 col-sm-2 offset-sm-10 mb-4">匯出</button>
					</div>
					<br>
				  <div class="table-responsive"><!-- bootstrap 修復 table 跑版 -->
						<table class="table  text-center">
						  <thead class="thead-green">
						  <tr class="text-center">
						      <th scope="col">編號</th> 
							  <th scope="col"><?php echo $lang->line("index.member_name"); ?></th>
							  <th scope="col">性別</th>
							  <th scope="col">所在棟別/可到樓層</th> 
						      <th scope="col"><?php echo $lang->line("index.room_bed_number"); ?></th> 
						      <th scope="col">備註</th> 
						      <th scope="col">所屬群組</th>
							  </tr>
						  </thead>
						  <tbody>
						    <?php 
								foreach($data as $row) 
								{
									$group_arr = array();
									$group_tmp = json_decode($row['group_id']);
									
									foreach($group_tmp as $v) {
										if($group_map[$v] != '') {
											$group_arr[] = $group_map[$v];
										}
									}
									
									$group_str = implode(',', $group_arr);
							?>
								<tr>
									<td scope='row'><?php echo $row['username'] ?></td> 
									<td scope='row'><?php echo $row['cname'] ?></td>
									<td scope='row'><?php echo $sex_map[$row['sex']] ?></td>
									<td scope='row'><?php echo $room_map[$row['room_strings']] ?></td>
									<td scope='row'><?php echo $row['room_strings'].'/'.$row['berth_number'] ?></td> 
									<td scope='row'></td> 
									<td scope='row'><?php echo $group_str ?></td>
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
								<div class="dataTables_paginate paging_simple_numbers text-center" id="dataTable_paginate">
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


</section>


<script>

function export_list() {
	
	$('#mform1').prop('action', 'new-groupsearch.php');
	$('#mform1').prop('method', 'get');
	
	$('#mform1').submit();
}

function export_group() {

	$('#mform1').prop('action', 'model/member_group_downlad.php');
	$('#mform1').prop('method', 'post');
	
	$('#mform1').submit();
}

</script>

<?php include('footer_layout.php'); ?>