<?php 
	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');
	
	$pagesize = 10;
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i';

	// 頁碼 	
	$sql = "SELECT count(*) as `count` FROM refund_date_logs";
	$rs  = $PDOLink->query($sql);
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
		$pageurl .= "<a href='?page=1'>".$lang->line("index.home")."</a> | <a href='?page={$prepage}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}'>".$lang->line("index.next_page")."</a> | <a href='?page={$pagenum}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT * FROM `system_info` WHERE `id` = 1";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetch();

	$refund_start  = date('Y-m-d', strtotime($data['price_start_date']));
	$refund_end    = date('Y-m-d', strtotime($data['price_end_date']));
	$refund_s_time = date('H:i',   strtotime($data['price_start_date']));
	$refund_e_time = date('H:i',   strtotime($data['price_end_date']));
	
	$sql = "SELECT *, (SELECT `cname` FROM `member` WHERE id = r.created_user LIMIT 0, 1) as 'cname' FROM `refund_date_logs` r 
			WHERE 1 ORDER BY created_at DESC Limit ". ($page-1) * $pagesize .",". $pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();	
	
?>
<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='powersetting.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>
	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">期末退費設定</h1>
	</div>


	<div class="row container-fluid mar-bot50">
	<?php if($_GET['success'] == 1){ ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong>設定成功!!</strong>
		</div>
	<?php } elseif ($_GET['error'] == 1) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
		  <strong>設定失敗!!</strong>
		</div>
	<?php } ?>
	</div>    


<!--期末退費設定-->
<div class="inner">   
	<div class="row">
		<div class="col-12">
			<div class="card shadow mb-4 ">

                <div class="card-body ">
					<form id="myForm" action="model/refund_period_upd.php" method="post">
						<label for="exampleInputPassword1"  class="label-center col btn-martop20">期末退費期間</label>
						<h4 class="mb-0 font-weight-bold text-center">選擇開始日期/時間</h4>
						<div class=" form-group  btn-martop20 form-inline">
							<label for="exampleInputPassword1"  class="label-center col-sm-1 offset-sm-1">日期</label>
							<input required="required" class="form-control col-sm-8 input-lg2" type="date" name="refund_start" value="<?php echo $refund_start ?>">
						</div>
						<div class=" form-group  btn-martop20 form-inline">
							<label for="exampleInputPassword1"  class="label-center  col-sm-1 offset-sm-1">時間</label>
							<input required="required" class="form-control col-sm-8 time-style" placeholder="hrs:mins"  type="time" name="refund_start_time" value="<?php echo $refund_s_time ?>">
						</div>

						<h4 class="mb-0 font-weight-bold h1-mar text-center">選擇結束日期/時間</h4>
						<div class=" form-group  btn-martop30 form-inline">
							<label for="exampleInputPassword1"  class="label-center  col-sm-1 offset-sm-1">日期</label>
							<input required="required" class="form-control col-sm-8 input-lg2" type="date" name="refund_end" value="<?php echo $refund_end ?>">
						</div>
						<div class=" form-group  btn-martop30 form-inline">
							<label for="exampleInputPassword1"  class="label-center col-sm-1 offset-sm-1">時間</label>
							<input required="required" class="form-control col-sm-8 time-style" placeholder="hrs:mins" type="time" name="refund_end_time" value="<?php echo $refund_e_time ?>">
						</div>
						<button type="submit"  class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-6 offset-3"
							onclick="return confirm('確認變更?')">變更設定
						</button> 
					</form>
                </div>

			</div>
		</div>
	</div>
</div>


<!--表格-->
<div class="inner">
	<div class=" row   table-mar">
		<div class="container  text-center">
			<h1 class="jumbotron-heading">歷程修改紀錄一覽</h1>
		</div>

		<div class="container-fluid  table-responsive" style='padding-left:0;'><!-- bootstrap 修復 table 跑版 -->
		<table class="table  text-center">
			<thead class="thead-green">
				  <tr class="text-center">
				  <th scope="col">建立日期</th> 
				  <th scope="col">開始時間～結束時間</th>
				  <th scope="col">備註</th> 
				  <th scope="col">設定者</th>

				</tr>

			</thead>
			<tbody>
				<?php 
				foreach($data as $v) {
					$cre_date = date($date_format, strtotime($v['created_at']));
					$between  = date($date_format.' '.$time_format, strtotime($v['refund_start']));
					$between .= " ~ ";
					$between .= date($date_format.' '.$time_format, strtotime($v['refund_end']));
				?>
				<tr>
					<td scope='row'><?php echo $cre_date ?></td>
					<td scope='row'><?php echo $between ?></td>
					<td scope='row'><?php echo $v['remark'] ?></td>
					<td scope='row'><?php echo $v['cname'] ?></td>
				</tr>
				<?php
				}
				?>							
			</tbody>
		</table>

		<!-- 跳頁 上下頁-->
			<div class="row">
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
			<!-- 跳頁 上下頁 END-->
		
		</div>

	</div>
</div>
<!--表格 END-->
</section>
<style>
.table-responsive{
	overflow-x:inherit;
}
@media screen and (max-width: 980px){
	.inner {
		max-width: 80%;
	}
}
</style>
<?php include('footer_layout.php'); ?>