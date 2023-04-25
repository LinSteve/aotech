<?php 
	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php'); 
		
	$pagesize = 10;
	
	// 管理員群組 id = 1 不顯示 -- 20200429
	$sql_kw   = " AND `enable` = '1' AND id != '1' ";
	$url_str  = '';
	$serach   = $_GET['serach'];
	
	$member_grp = array();
	
	if(isset($_GET['member_grp'])) {
		
		$member_grp = $_GET['member_grp'];
		
		$sql_in  = implode(',', $member_grp);
		$sql_kw .= " AND id IN ({$sql_in}) ";
		
		foreach($member_grp as $v) {
			$url_str .= "&member_grp[]=".$v;
		}
	}
	
	$sql = "SELECT count(*) as `count` FROM `group` WHERE 1 ".$sql_kw;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$row_tmp = $rs->fetch();
	$rownum  = $row_tmp['count'];
	
	if(isset($_GET['page'])) {
		$page = $_GET['page'];  
	} else {
		$page = 1;                                 
	}
	
	$sql = "SELECT * FROM `group` WHERE 1 {$sql_kw} LIMIT ". ($page-1) * $pagesize .",". $pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$rs_data = $rs->fetchAll();

	$pageurl  = '';
	$pagenum  = (int) ceil($rownum / $pagesize);  
	$prepage  = $page - 1;                        
	$nextpage = $page + 1;
	
	if($page == 1) {                         
		$pageurl .= " ".$lang->line("index.home")." | ".$lang->line("index.previous_page")." | ";
	} else {
		$pageurl .= "<a href='?page=1&serach={$serach}'>".$lang->line("index.home")."</a> | <a href='?page=".$prepage.$url_str."&serach={$serach}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page=".$nextpage.$url_str."&serach={$serach}'>".$lang->line("index.next_page")."</a> | <a href='?page=".$pagenum.$url_str."&serach={$serach}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT * FROM `group` WHERE 1 AND `enable` = '1' AND id != '1'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$group_arr = $rs->fetchAll();
	
	for($i = 1; $i <= 6; $i++) {
		eval("\$opt".$i." = array();");
	}
	
	$sql = "SELECT * FROM `menu_list`";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$tmp = $rs->fetchAll();

	foreach($tmp as $v) {
		
		$id        = $v['id'];
		$category  = $v['category'];
		$item_name = $v['item_name'];
		
		switch($category) {
			
			case '1':
				$opt1[$id] = $item_name;
				break;
			case '2':
				$opt2[$id] = $item_name;
				break;
			case '3':
				$opt3[$id] = $item_name;
				break;
			default:
				break;
		}
	}
	
	$sql = "SELECT * FROM `door`";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$tmp = $rs->fetchAll();

	foreach($tmp as $v) {
		
		$id        = $v['id'];
		$item_name = $v['name'];
		$opt4[$id] = $item_name;
	}

	$sql = "SELECT * FROM `elevator`";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$tmp = $rs->fetchAll();

	foreach($tmp as $v) {
		
		$id        = $v['id'];
		$item_name = $v['name'];
		$opt5[$id] = $item_name;
	}
	
	$sql = "SELECT * FROM `room`";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$tmp = $rs->fetchAll();

	foreach($tmp as $v) {
		
		$id        = $v['id'];
		$item_name = $v['name'];
		$opt6[$id] = $item_name;
	}
?>
<section id="main" class="wrapper">

	<!-- <div class="col-12"><a href='group_management.php'><i class='fas fa-chevron-circle-left fa-3x'></i></a></div> 
	<h2 style="margin-top: -30px;" align="center">群組編輯</h2><br>-->
	<div class='col-12 btn-back'><a href='roomgroup.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">群組權限編輯</h1>
	</div>
	
	<div class="row container-fluid mar-bot50">
	<?php if($_GET['success'] == 1){ ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong>群組設定完成</strong>
		</div>
	<?php } elseif ($_GET['error'] == 1) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
		  <strong>群組設定失敗</strong>
		</div>
	<?php } elseif ($_GET['success'] == 4) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong>刪除群組完成</strong>
		</div>
	<?php } elseif ($_GET['error'] == 2) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
		  <strong><?php echo $lang->line("index.Incorrect-or-non-existent-data");?></strong> 
		</div>
	<?php } elseif ($_GET['error'] == 4) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
		  <strong>刪除群組失敗</strong>
		</div>
	<?php } ?>
	</div>

<!-- 查詢 -->
<div class="inner">
	<div class="row">
		<div class='col-12'>
			<form method="get">
		
<!--前端查詢畫面--->
<div class='panel-body'>
	<div class='form-group row form-mar'>
		<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right pd-top25' >群組名稱</label>
		<div class='col-sm-9  form-inline'> 
			<select class='form-control selectpicker col'  title='全部' size='1' name='member_grp[]' multiple>
			<?php
				foreach($group_arr as $v) {
					$select = in_array($v['id'] , $member_grp) ? 'selected' : '';
					echo "<option value='{$v['id']}' {$select}>{$v['name']}</option>";
				}
			?>
			</select>
		</div>
	</div>
	<br><br>
	<input type='hidden' name='serach' value='1'>
	<button type='submit' class='btn  btn-loginfont btn-primary2  col-4 offset-4' >
		<?php echo $lang->line("index.confirm_query") ?>
	</button>
</div>
			</form>
		</div>
	</div>
</div>
<!-- 查詢 END-->

<!-- TABLE-->
	
<div class="inner inner2" style="display:<?php echo ($serach != '') ? 'block' : 'none'?>">
	<div class="row" id='search-after'>
	<div class='col-12'>
	<h1 class="jumbotron-heading text-center h1-mar"><?php echo ($serach != '') ? $lang->line("serach_results") : "" ?></h1>
																
		<form id='mform2' action=''   class="col-12 table-responsive">
		<table class="table  text-center">
		<thead class="thead-green">
			<tr class="text-center">
			  <!-- <th scope="col" >群組轉移</th>-->
			  <th scope="col">最新更新日</th>
			  <th scope="col">群組名稱</th>
			  <th scope="col">用途說明</th>
			  <th scope="col">名單群組管理</th>
			  <th scope="col">門禁管理</th>
			  <th scope="col">電力管理</th>
			  <th scope="col">宿舍門禁</th>
			  <th scope="col">電梯設置</th>
			  <!--<th scope="col">房號</th>-->
			  <th scope="col">備註</th>
			  <th scope="col">操作</th>
			</tr>
		</thead>
<?php			
			foreach($rs_data as $v) 
			{
				
				for($i = 1; $i <= 6; $i++) {
					eval("\$opt".$i."_arr = array();");
					eval("\$opt".$i."_str = '';");
				}
				
				for($i = 1; $i <= 6; $i++) {
					eval("\$opt".$i."_all = '';");
				}
				
				$opt1_all = implode(',', $opt1);
				$opt2_all = implode(',', $opt2);
				$opt3_all = implode(',', $opt3);
				$opt4_all = implode(',', $opt4);
				$opt5_all = implode(',', $opt5);
				$opt6_all = implode(',', $opt6);
				
				$menu_access = json_decode($v['menu_access']);
				$door_access = json_decode($v['door_id']);
				$elevator    = json_decode($v['elevator_id']);
				$room_access = json_decode($v['room_id']);

				foreach($opt1 as $key => $val) {
					if(in_array($key, $menu_access)) {
						$opt1_arr[] = $val;
					}
				}
				
				foreach($opt2 as $key => $val) {
					if(in_array($key, $menu_access)) {
						$opt2_arr[] = $val;
					}
				}
				
				foreach($opt3 as $key => $val) {
					if(in_array($key, $menu_access)) {
						$opt3_arr[] = $val;
					}
				}
				
				foreach($opt4 as $key => $val) {
					if(in_array($key, $door_access)) {
						$opt4_arr[] = $val;
					}
				}
				
				foreach($opt5 as $key => $val) {
					if(in_array($key, $elevator)) {
						$opt5_arr[] = $val;
					}
				}

				foreach($opt6 as $key => $val) {
					if(in_array($key, $room_access)) {
						$opt6_arr[] = $val;
					}
				}
				
				$opt1_str = implode(',', $opt1_arr);
				$opt2_str = implode(',', $opt2_arr);
				$opt3_str = implode(',', $opt3_arr);
				$opt4_str = implode(',', $opt4_arr);
				$opt5_str = implode(',', $opt5_arr);
				$opt6_str = implode(',', $opt6_arr);
				
				echo "
					<tr class='editgroup'>
					  <td scope='col'>{$v['update_date']}</td>
					  <td scope='col'>{$v['name']}</td>
					  <td scope='col'>{$v['usage']}</td>
					  <td scope='col'>".$opt1_str."</td>
					  <td scope='col'>".$opt2_str."</td>
					  <td scope='col'>".$opt3_str."</td>
					  <td scope='col'>".$opt4_str."</td>
					  <td scope='col'>".$opt5_str."</td>
					  <td scope='col'>{$v['remark']}</td>
					  <td scope='col'>
						<a href='group_edit.php?id={$v['id']}' class='btn' title='權限編輯'><i class='fas fa-pencil-alt text-orange'></i></a>
						<a href='#' class='btn' onclick='del_group({$v['id']})' title='刪除'><i class='fas fa-trash-alt text-orange'></i></a>
					  </td>
					</tr>";
			}
?>
		</form>
		</table>
	</div>
	</div>

	<!-- 跳頁 上下頁-->
	<div class="col-12">
		<div class=" text-center" id="dataTable_paginate">
		<?php
			if($rownum > $pagesize){   
				echo $pageurl;
				echo " ".$lang->line("index.current_page")." $page | ".$lang->line("index.a_few_pages")." $pagenum ".$lang->line("index.page")."";
			}
		?>									  
		</div>
	</div>

<!-- 跳頁 上下頁 END-->
</div>
	
</section>

<script>

function del_group(id) {
	
	if(confirm('確定刪除?')) {
		location.replace('model/member_group_delete.php?id=' + id);
	} 
	
	return false;
}
</script>

<?php include('footer_layout.php'); ?>