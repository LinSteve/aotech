<?php
    include('includes/header.php');
    include('includes/nav.php');
	include('includes/scripts.php');
	
	$pagesize    = 10;
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';
	
	$room_num    = $_GET['room_strings'];
	$username    = $_GET['username'];
	$start_date  = $_GET['start_date'];
	$end_date    = $_GET['end_date'];
	$status      = $_GET['status'];
	$search      = $_GET['search'];
	
	if($room_num) {
		$sql_kw .= " AND room_number LIKE '%{$room_num}%' ";
	}

	if($username) {
		$sql_kw .= " AND username_number = '{$username}' ";
	}
	
	if($start_date) { 
		$s_time  = date('Y-m-d', strtotime($start_date));
		$sql_kw .= " AND add_date > '{$s_time}' "; 
	}
	
	if($end_date)   { 
		$e_time  = date('Y-m-d', strtotime($end_date." +1 day"));
		$sql_kw .= " AND add_date < '{$e_time}' "; 
	}
	
	if($status) {
		$sql_kw .= " AND data_type = '{$status}' "; 
	}
	
	$sql = "SELECT COUNT(*) as 'count' FROM `content_us` WHERE 1 ".$sql_kw;
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
		$pageurl .= "<a href='?page=1&room_strings={$room_num}&username={$username}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.home").
					"</a> | <a href='?page={$prepage}&room_strings={$room_num}&username={$username}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}&room_strings={$room_num}&username={$username}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.next_page").
					"</a> | <a href='?page={$pagenum}&room_strings={$room_num}&username={$username}&start_date={$start_date}&end_date={$end_date}&status={$status}&search={$search}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT * FROM `content_us` WHERE 1 {$sql_kw} ORDER BY id DESC LIMIT ".($page-1)* $pagesize . ",".$pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data_arr = $rs->fetchAll();
	
	$sql = "SELECT * FROM `custom_variables` WHERE `custom_catgory` = 'contact_status'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$status_type = $rs->fetchAll();
	$status_map  = array();
	
	foreach($status_type as $v) {
		$status_map[$v['custom_id']] = $v['custom_var'];
	}
?>

  <!-- Begin Page Content -->
  <div class="container-fluid">

          <!-- Page Heading -->
		  <h1 class="mb-2 font-weight-bold">客服中心</h1>
	
		  
    	<div class="container-fluid mar-bot25 mar-center2">
			<?php if($_GET['success'] == 1){ ?>
				<div style="margin: 0 auto; text-align: center; " class="alert alert-success" role="alert">
					<strong>寄信成功!!</strong>
				</div>
			<?php } elseif ($_GET['error'] == 1) { ?>
				<div style="margin: 0 auto; text-align: center; " class="alert alert-danger" role="alert">
					<strong>寄信失敗!!</strong>
				</div>
			<?php } elseif ($_GET['error'] == 2) { ?>
				<div style="margin: 0 auto; text-align: center; " class="alert alert-danger" role="alert">
					<strong>查無未處理單!!</strong>
				</div>
			<?php } ?>
		</div>

		<!-- SEARCH -->
		<div class='col-12'>
				<form id='mform1' action="" method="get">

					<div class='form-group row '>
						<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'><?php echo $lang->line("index.room_bed_number");?></label>
						<div class='col-sm-8 input-group-lg'> 
							<input  type='text' class='form-control' name='room_strings' placeholder='全部' value='<?php echo $room_num ?>'>
						</div>
					</div>
							

					<div class='form-group row '>
						<label for='exampleFormControlInput1'  class='col-sm-2 col-form-label label-right'>學號</label>
						<div class='col-sm-8 input-group-lg'> 
							<input  type='text' maxlength='10' class='form-control  col' name='username' placeholder='全部' value='<?php echo $username ?>'>  
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
							<option value=''>全部</option>
							<?php
								foreach($status_type as $v) {
									$opt_key = $v['custom_id'];
									$opt_val = $v['custom_var'];
									$select  = ($opt_key == $status) ? 'selected' : '';
									echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
								}
							?>
							</select>
						</div>
					</div>					
					
					<br>
					<input type='hidden' name='search' value='1'>
					<button type='button' onclick='export_list()' class='btn btnfont-30 btn-primary2 text-white col-sm-4 offset-sm-4'>查詢</button>
				</form>
	    </div>
		<br>

		<!-- SEARCH END-->
		<div class=" text-right " style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
				<button type="button" onclick='export_file()' class="btn btnfont-30 text-white btn-primary2 col-sm-2">匯出</button>
		</div>
		<br>

          <!--Table--->
          <div class="table-responsive" style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
                      <table class="table  text-center font-weight-bold">
                        <thead class="thead-green">
                        <tr class="text-center">
                          <th scope="col">報修日期</th>
                          <th scope="col">處理日期</th>
                          <th scope="col">處理人員</th>
                          <th scope="col"><?php echo $lang->line("index.room_bed_number");?></th>
						  <th scope="col">姓名/學號</th>
                          <th scope="col">電話/e-mail</th>
                          <th scope="col">報修-儲值主機</th>
                          <th scope="col">報修-房內卡機</th>
                          <th scope="col">狀態</th>
                          <th scope="col">備註</th>
                          <th scope="col">操作</th>
                        </tr>
                        </thead>
                        <tbody class='contact'>
						<?php
							foreach($data_arr as $v) {
								
								$showdate = date($date_format.' '.$time_format, strtotime($v['add_date']));
								$showupd  = $v['update_date'] != '' ? date($date_format, strtotime($v['update_date'])) : '';
								$replier  = $v['replier'];
								$showname = $v['title'].'/'.$v['username_number'];
								$showmail = $v['phone'].'/'.$v['email'];
								$room_num = $v['room_number'];
								$d_type   = $v['data_type'];
								$desc     = $status_map[$d_type];
								$remark   = $v['remark'];
								// $link     = $d_type != '2' ? "<a href='contact_us_edit.php?id={$v['id']}' class='btn  text-orange'  data-toggle='tooltip' data-placement='bottom' title='編輯'><i class='fas fa-pencil-alt'></i></a>" : '';
								$link     = "<a href='contact_us_edit.php?id={$v['id']}' class='btn  text-orange'  data-toggle='tooltip' data-placement='bottom' title='編輯'><i class='fas fa-pencil-alt'></i></a>";
								$host_msg = $v['host_type'].' '.$v['host_other'];
								$room_msg = $v['room_type'].' '.$v['room_other'];
						?>
                          <tr>
                            <td><?php echo $showdate ?></td>
                            <td><?php echo $showupd ?></td>
                            <td><?php echo $replier ?></td>
                            <td><?php echo $room_num ?></td>
							<td><?php echo $showname ?></td>
                            <td><?php echo $showmail ?></td>
                            <td><?php echo $host_msg ?></td>
                            <td><?php echo $room_msg ?></td>
                            <td><?php echo $desc ?></td>
                            <td><?php echo $remark ?></td>
							<td><?php echo $link ?></td>
						<?php	
							}
						?>
                          </tbody>
                      </table>
          </div>
		  
						<div class="row " style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
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
	
	$('#mform1').prop('action', 'content_us.php');
	$('#mform1').prop('method', 'get');
	$('#mform1').submit();
}

function export_file() {

	$('#mform1').prop('action', 'model/contact_download.php');
	$('#mform1').prop('method', 'get');
	$('#mform1').submit();
}

</script>

<?php include('includes/footer.php'); ?>