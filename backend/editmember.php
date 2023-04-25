<?php

    include('includes/header.php');
    include('includes/nav.php');
	
	$pagesize = 10;
	
	$sql_kw   = " AND username != '".WEBADMIN."' ";
	
 	$cname    = $_GET['cname'];
 	$username = $_GET['username'];
 	$room_num = $_GET['room_strings'];
	$resident = $_GET['resident'];
	$serach   = $_GET['serach'];
	
	if($username) { $sql_kw .= " AND (`username` like '%".trim($username)."%' OR `id_card` like '%".trim($username)."%') "; }
	if($cname)    { $sql_kw .= " AND `cname` like '%".trim($cname)."%' "; }
	if($room_num) { $sql_kw .= " AND concat(room_strings, berth_number) like '%".trim($room_num)."%' "; }
	
	if($resident != '') {
		
		switch($resident) {
			
			case '1':
				$sql_kw .= " AND del_mark = '0' AND `identity` = '1' ";
				break;
			case '2':
				$sql_kw .= " AND del_mark = '0' AND `room_strings` != '' AND `identity` = '0'";
				break;
			case '3': // 非住宿生 + 離宿 -- 20200812
				$sql_kw .= " AND (`room_strings` = '' OR del_mark = '1') AND `identity` IN (0, 1)";
				break;
			case '4': // 研習卡
				$sql_kw .= "AND del_mark = '0' AND `identity` = '4' ";
				break;				
			case '5': // 公用卡
				$sql_kw .= " AND del_mark = '0' AND `identity` = '5' ";
				break;				
			default:
				break;
		}
	}
	
	if(isset($_GET['page'])) {
		$page = $_GET['page'];  
	} else {
		$page = 1;                                 
	}
	
	$sql = "SELECT count(*) as 'count' FROM `member` WHERE 1 ".$sql_kw;
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
		$pageurl .= "<a href='?page=1&cname={$cname}&username={$username}&room_strings={$room_num}&resident={$resident}&serach={$serach}'>".$lang->line("index.home")."</a> | 
					 <a href='?page={$prepage}&cname={$cname}&username={$username}&room_strings={$room_num}&resident={$resident}&serach={$serach}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}&cname={$cname}&username={$username}&room_strings={$room_num}&resident={$resident}&serach={$serach}'>".$lang->line("index.next_page")."</a> | 
					 <a href='?page={$pagenum}&cname={$cname}&username={$username}&room_strings={$room_num}&resident={$resident}&serach={$serach}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT * FROM `member` WHERE 1 {$sql_kw} ORDER BY `id` Limit ". ($page-1) * $pagesize .",". $pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	$sql = "SELECT * FROM `group`";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$group_arr = $rs->fetchAll();
	$group_map = array();
	
	foreach($group_arr as $v) {
		$group_map[$v['id']] = $v['name'];
	}
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'sex'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$sex_arr = $rs->fetchAll();
	$sex_map = array();
	
	foreach($sex_arr as $v) {
		$sex_map[$v['custom_id']] = $v['custom_var'];
	}
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'login'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$login_arr = $rs->fetchAll();
	$login_map = array();
	
	foreach($login_arr as $v) {
		$login_map[$v['custom_id']] = $v['custom_var'];
	}
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'resident'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$resident_arr = $rs->fetchAll();
	$resident_map = array();
	
	foreach($resident_arr as $v) {
		$resident_map[$v['custom_id']] = $v['custom_var'];
	}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

        <!-- Page Heading -->
		<h1 class="mb-2 font-weight-bold">修改名單資料</h1>
		<!--
        <p class="text-lg text-center font-weight-bold NG-color">
			比照前台修改名單資料的處理方式，但會多一欄餘額(編輯時可修改)
		</p>
		-->
		
        <div class="row container-fluid mar-bot50">
		<?php if($_GET['error'] == 1){ ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
				<strong>Error！！</strong>
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
		
		<!-- SEARCH 修改名單資料-->
		<div class='col-12'>
				<form method="get">
						<div class='panel-body'>
								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'><?php echo $lang->line("index.room_bed_number")?></label>
									<div class='col-sm-8 input-group-lg'> 
										<input type='text' class='form-control'  name='room_strings' placeholder='全部' value='<?php echo $room_num ?>'>
									</div>
								</div>		

								<div class='form-group row'>
									<label for='exampleFormControlInput1'  class='col-sm-2 col-form-label label-right' >編號</label>
									<div class='col-sm-8 input-group-lg'> 
										<input type='text' class='form-control  col'  maxlength='10'  name='username' placeholder='全部學號/教職員工/卡號' value='<?php echo $username ?>'>
									</div>
								</div>

								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right' >姓名</label>
									<div class='col-sm-8 input-group-lg'>
										<input   type='text' class='form-control'   name='cname' placeholder='全部' value='<?php echo $cname ?>'>  
									</div>
								</div>

								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right pd-top25'>身分</label>
									<div class='col-sm-8 form-inline'> 
										<select  class='col form-control selectpicker custom-select-lg' size='1' name='resident'>
										<option value=''>全部</option>
										<?php
											foreach($resident_arr as $v) {
												
												$opt_key = $v['custom_id'];
												$opt_val = $v['custom_var'];
												$select  = ($opt_key == $resident) ? 'selected' : '';
												
												echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
											}
										?>
										</select>
									</div>
								</div>

									<br>
									<input type='hidden' name='serach' value='1'>
									<button type='submit' class='btn btnfont-30 btn-primary2 text-white col-sm-4 offset-sm-4'>查詢</button>
						</div>
				</form>
	    </div>
		<br>
		<!-- SEARCH 修改名單資料 END-->

        <!--Table--->
        <div class="table-responsive" style="display:<?php echo ($serach != '') ? 'block' : 'none'?>">
                      <table class="table  text-center font-weight-bold">
                        <thead class="thead-green">
                        <tr class="text-center">
                          <th scope="col">學號/教職員工編號</th> 
                          <th scope="col">姓名</th>
                          <th scope="col">性別</th>
                          <th scope="col">卡號</th>
                          <th scope="col">房號</th>
                          <th scope="col"><?php echo $lang->line("index.bed_number");?></th>
                          <th scope="col">餘額</th>
                          <th scope="col">備註</th>
                          <th scope="col">群組</th>
                          <th scope="col">操作</th>
                        </tr>
                        </thead>
                        <tbody>
						<?php 
							foreach($data as $row) {
								
								$row_id    = $row['id'];
								$ident_tmp = $row['identity'];
								$identity  = $login_map[$ident_tmp];
								$balance   = $row['balance'];
								$group_tmp = json_decode($row['group_id']);
								$group_str = '';
								foreach($group_tmp as $v) {
									$group_str .= $group_map[$v].",";
								}
								$group_str = substr($group_str, 0, -1);
						?>
                          <tr>
							<td><?php echo $row['username'] ?></td>
							<td><?php echo $row['cname'] ?></td>
							<td><?php echo $sex_map[$row['sex']] ?></td>
							<td><?php echo $row['id_card'] ?></td>
							<td><?php echo $row['room_strings'] ?></td>
							<td><?php echo $row['berth_number'] ?></td>
							<td><?php echo $balance ?></td>
							<td><?php echo $identity ?></td>
							<td><?php echo $group_str ?></td>
                            <td>
								<a href='member_edit.php?id=<?php echo $row_id ?>' class='btn  text-orange' data-toggle='tooltip' data-placement='bottom' title='編輯'><i class='fas fa-pencil-alt'></i></a>
							</td>
						  </tr>
						<?php
							}
						?>
                          </tbody>
                      </table>
		</div>
					  
			<div class='text-center'  id="dataTable_paginate" style="display:<?php echo ($serach != '') ? 'block' : 'none'?>">
			<?php  
			if($rownum > $pagesize) {
				echo $pageurl;
				echo " ".$lang->line("index.current_page")." $page | ".$lang->line("index.a_few_pages")." $pagenum ".$lang->line("index.page")."";
			}
			?> 
			</div>
			
		



</div>
<!-- /.container-fluid -->


<?php
    include('includes/scripts.php');
    include('includes/footer.php');
?>