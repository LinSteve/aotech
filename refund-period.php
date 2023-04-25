<?php 

	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

	$pagesize = 10;
	
	$date_format = 'Y/m/d';

	// 頁碼 	
	$sql = "SELECT count(*) FROM refund_interval_setting";
	$rs  = $PDOLink->query($sql);
	$rownum = $rs->fetchcolumn();

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
	
	$data  = array();
	$data0 = array();
	$sql   = "SELECT * FROM refund_interval_setting WHERE id <= 7 ORDER BY id";
	$rs    = $PDOLink->prepare($sql);
	$rs->execute();
	$data1 = $rs->fetchAll();
	
	$sql   = "SELECT * FROM refund_interval_setting WHERE id > 7 ORDER BY id DESC";
	$rs    = $PDOLink->prepare($sql);
	$rs->execute();
	$data2 = $rs->fetchAll();
	
	foreach($data1 as $v) {
		$data0[] = $v;
	}

	foreach($data2 as $v) {
		$data0[] = $v;
	}
	
	for($i = $prepage * $pagesize; ($i < $prepage * $pagesize + $pagesize) & ($i < $rownum); $i++) {
		$data[] = $data0[$i];
	}

	$sql = "SELECT * FROM custom_variables WHERE custom_catgory = 'week' ";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$week_arr = $rs->fetchAll();
	$week_map = array();
	
	foreach($week_arr as $v) {
		$week_map[$v['custom_id']] = $v['custom_var'];
	}
?>

<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='powersetting.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>
	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">退費時段設定</h1>
	</div>


	<div class="row container-fluid mar-center2">
	<?php if($_GET['success'] == 1) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong>更新成功!!</strong>
		</div>
	<?php } elseif($_GET['success'] == 2) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong>更新成功!!</strong>
		</div>
	<?php } elseif($_GET['success'] == 3) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong>刪除完成!!</strong>
		</div>
	<?php } elseif($_GET['error'] == 1) { ?> 
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
		  <strong>更新失敗!!</strong>
		</div>	
	<?php } elseif($_GET['error'] == 2) { ?> 
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
		  <strong>資料輸入錯誤或不存在!!</strong>
		</div>	
	<?php } ?>
	</div>    


<!--退費時段設定-->
<div class="inner">   
	<div class="row">
		<!--
		div class="col-12">
			<div class="card shadow mb-4 ">

                <div class="card-body ">
					<form id="myForm" action="#" method="get"  >
						<label for=""  class="label-center col btn-martop20">指定時段新增</label>
						
						<div class=" form-group  btn-martop30 form-inline">
							<label for="exampleInputPassword1"  class="label-center col-2 ">指定日期</label>
							<input class="form-control col-8 input-lg2" type="date" name="price_start_date" value="">
						</div>

						<div class=" form-check form-group  btn-martop20 col">
							<label for=""  class="label-center col btn-martop20">排程1</label>

							<input id='checkbox1' class='form-control offset-2'  type='checkbox' name='username'>
							<label for='checkbox1' class=' col-form-label '>&nbsp;&nbsp;不啟用</label>
						</div>

						<div class=" form-group  btn-martop20 form-inline">
							<label for="exampleInputPassword1"  class="label-center col-2  ">開始時間</label>
							<input class="form-control col-8 input-lg2" type="time" name="one_time_a" placeholder="hrs:mins" value="06:00" >
						</div>
						
						<div class=" form-group  btn-martop30 form-inline">
							<label for="exampleInputPassword1"  class="label-center col-2 ">結束時間</label>
							<input class="form-control col-8 input-lg2" type="time" name="one_time_b" placeholder="hrs:mins" value="11:00">
						</div>


						<div class=" form-check form-group  btn-martop20 col">
							<label for=""  class="label-center col btn-martop20">排程2</label>

							<input id='checkbox2' class='form-control offset-2'  type='checkbox' name='username'>
							<label for='checkbox2' class=' col-form-label '>&nbsp;&nbsp;不啟用</label>
						</div>

						<div class=" form-group  btn-martop20 form-inline">
							<label for=""  class="label-center col-2  ">開始時間</label>
							<input class="form-control col-8 input-lg2" type="time" name="one_time_a" placeholder="hrs:mins" value="06:00" >
						</div>
						
						<div class=" form-group  btn-martop30 form-inline">
							<label for=""  class="label-center col-2 ">結束時間</label>
							<input class="form-control col-8 input-lg2" type="time" name="one_time_b" placeholder="hrs:mins" value="11:00">
						</div>

						<button type="submit"  class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-6 offset-3">確認新增</button> 

					</form>
                </div>

			</div>
		</div>
		-->

	</div>
</div>




<!--表格-->
<div class='inner'>

		<div class=" row   table-mar">
			<div class="container-fluid">
				<h1 class="jumbotron-heading text-center">目前已設定排程</h1>
				<div class="text-right">
				<button type="button" class="btn btn-loginfont btn-primary2 btn-marbot20 col-sm-3 offset-sm-9" onclick="location.href='refund-period-add.php'">
					<!--<i class="fas fa-plus-circle"></i>-->新增指定時段
				</button>
				</div>
			</div>

			<div class="container-fluid  table-responsive" style='padding-left:0;'><!-- bootstrap 修復 table 跑版 -->
				<table   class="table  table-condensed  text-center">
					<thead class="thead-green">
						  	<tr class="text-center">
						      <th scope="col">星期/指定日期</th> 
							  <th scope="col">開始時間～結束時間</th>
							  <th scope="col">備註</th> 
							  <th scope="col"><?php echo $lang->line("index.operating"); ?></th>

							</tr>

					</thead>
					<tbody class='refund-period' >
							<?php
							foreach($data as $v) {
								
								$week_title  = '';
								$week_day    = $v['day'];
								$schedule_id = $v['id'];
								
								if($week_map[$week_day] != '') {
									$week_title = $week_map[$week_day];
								} else {
									// $tmp_date   = str_pad($week_day,4,'0',STR_PAD_LEFT);
									$week_title = date($date_format, strtotime($week_day));
								}
								
								$schedule = array();
								$sche_tmp = $v['time'];
								$sche_arr = explode('{', $sche_tmp);
								
								foreach($sche_arr as $m) {
									$tmp_arr = explode('}', $m);
									if($tmp_arr[0] != '') {
										$schedule[] = $tmp_arr[0];
									}
								}
							?>
								<tr>
									<td scope='row'><?php echo $week_title ?></td>
									<td scope='3'>
										<div>
											<?php 
												foreach($schedule as $key => $val) {
													$desc_str = "<b class='b-label'>排程%s</b><span>%s</span><br>";
													if(strpos($val, '^') > -1) {
														echo sprintf($desc_str, $key+1, "不啟用");
													} else {
														echo sprintf($desc_str, $key+1, $val);
													}
												}
											?>
										</div>											
									</td>
									<td scope='row'></td>
									<td scope='row' class='text-center'>
										<a href='refund-period-edit.php?id=<?php echo $schedule_id ?>' class='btn ' data-toggle="tooltip" data-placement="bottom" title='編輯'><i class='fas fa-pencil-alt text-orange' ></i></a>
										<?php
										if($schedule_id > 7) {
										?>
											<a onclick="return confirm('您確定要刪除嗎?');"  href='model/refund_interval_del.php?id=<?php echo $schedule_id; ?>' class='btn '  data-toggle="tooltip" data-placement="bottom" title='刪除'><i class='fas fa-trash-alt  text-orange'></i></a>
										<?php
										}
										?>
									</td>
								</tr>								
						<?php
							}
						?>

										<!--
											<tr>
												<td scope='row'>星期一</td>
												<td scope='3'>
													<div>
														<b class='b-label'>排程1</b><span>&nbsp;13:30 ~ 15:30</span><br>
														<b class='b-label'>排程2</b><span>&nbsp;18:00 ~ 23:30</span>
													</div>											
												</td>
												<td scope='row'>星期二～日往下排列(可參考舊版宿舍的退費時段設定)。 
																星期日後一筆為最新有效的指定時段
												</td>
												
												<td scope='row' class='text-left'>
													<a href='refund-period-edit.php' class='btn ' title='編輯'><i class='fas fa-pencil-alt text-orange' ></i></a>
												</td>
											</tr>
											<tr>
											<td scope='row'>星期二</td>
											<td scope='row'>
												<div>
												<b class='b-label'>排程1</b><span>&nbsp;07:30 ~ 13:00</span><br>
												<b class='b-label'>排程2</b><span>&nbsp;不啟用&emsp;&emsp;&emsp;</span><br>
							    	        	</div>
											</td>
											<td scope='row'>平時 一～日背景色要一樣</td>
											<td scope='row' class='text-left'>
												<a href='refund-period-edit.php' class='btn' title='編輯'><i class='fas fa-pencil-alt text-orange' ></i></a>
											</td>
											</tr>

											<tr>
											<td scope='row'>星期三</td>
											<td scope='row'>
												<div>
												<b class='b-label'>排程1</b><span>&nbsp;07:30 ~ 13:00</span><br>
												<b class='b-label'>排程2</b><span>&nbsp;不啟用&emsp;&emsp;&emsp;</span><br>
							    	        	</div>
											</td>
											<td scope='row'>平時 一～日背景色要一樣</td>
											<td scope='row' class='text-left'>
												<a href='refund-period-edit.php' class='btn' title='編輯'><i class='fas fa-pencil-alt text-orange' ></i></a>
											</td>
											</tr>



											<tr>
											<td scope='row'>星期四</td>
											<td scope='row'>
												<div>
												<b class='b-label'>排程1</b><span>&nbsp;07:30 ~ 13:00</span><br>
												<b class='b-label'>排程2</b><span>&nbsp;不啟用&emsp;&emsp;&emsp;</span><br>
							    	        	</div>
											</td>
											<td scope='row'>平時 一～日背景色要一樣</td>
											<td scope='row' class='text-left'>
												<a href='refund-period-edit.php' class='btn' title='編輯'><i class='fas fa-pencil-alt text-orange' ></i></a>
											</td>
											</tr>

											<tr>
											<td scope='row'>星期五</td>
											<td scope='row'>
												<div>
												<b class='b-label'>排程1</b><span>&nbsp;07:30 ~ 13:00</span><br>
												<b class='b-label'>排程2</b><span>&nbsp;不啟用&emsp;&emsp;&emsp;</span><br>
							    	        	</div>
											</td>
											<td scope='row'>平時 一～日背景色要一樣</td>
											<td scope='row' class='text-left'>
												<a href='refund-period-edit.php' class='btn' title='編輯'><i class='fas fa-pencil-alt text-orange' ></i></a>
											</td>
											</tr>





											<tr>
											<td scope='row'>星期六</td>
											<td scope='row'>
												<div>
												<b class='b-label'>排程1</b><span>&nbsp;07:30 ~ 13:00</span><br>
												<b class='b-label'>排程2</b><span>&nbsp;不啟用&emsp;&emsp;&emsp;</span><br>
												<b class='b-label'>排程3</b><span>&nbsp;18:00 ~ 23:30</span>
							    	        	</div>
											</td>
											<td scope='row'>平時 一～日背景色要一樣</td>
											<td scope='row' class='text-left'>
												<a href='refund-period-edit.php' class='btn' title='編輯'><i class='fas fa-pencil-alt text-orange' ></i></a>
											</td>
											</tr>



											<tr>
											<td scope='row'>星期日</td>
											<td scope='row'>
												<div>
												<b class='b-label'>排程1</b><span>&nbsp;07:30 ~ 13:00</span><br>
												<b class='b-label'>排程2</b><span>&nbsp;不啟用&emsp;&emsp;&emsp;</span><br>
												<b class='b-label'>排程3</b><span>&nbsp;18:00 ~ 23:30</span>
							    	        	</div>
											</td>
											<td scope='row'>平時 一～日背景色要一樣</td>
											<td scope='row' class='text-left'>
												<a href='refund-period-edit.php' class='btn' title='編輯'><i class='fas fa-pencil-alt text-orange' ></i></a>
												
											</td>
											</tr>
											<tr>
											<td scope='row'>2020/06/02</td>
											<td scope='row'>
												<div>
													<b class='b-label'>排程1</b><span>&nbsp;13:30 ~ 15:30</span><br>
													<b class='b-label'>排程2</b><span>&nbsp;18:00 ~ 23:30</span>
												</div>
											</td>
											<td scope='row'>指定</td>
											<td scope='row' class='text-left'>
												<a href='refund-period-edit.php' class='btn ' title='編輯'><i class='fas fa-pencil-alt text-orange' ></i></a>
												<a onclick="return confirm('您確定要刪除嗎?');"  href='memberUpd.php?id=<?php echo $row[id]; ?>&get_act=del' class='btn '  title='刪除'><i class='fas fa-trash-alt  text-orange'></i></a>
											<tr>
											<td scope='row'>2020/05/30</td>
											<td scope='row'>
												<div>
													<b class='b-label'>排程1</b><span>&nbsp;13:30 ~ 15:30</span><br>
													<b class='b-label'>排程2</b><span>&nbsp;18:00 ~ 23:30</span>
												</div>
											</td>
											<td scope='row'>指定</td>
											<td scope='row' class='text-left'>
												<a href='refund-period-edit.php' class='btn ' title='編輯'><i class='fas fa-pencil-alt text-orange' ></i></a>
												<a onclick="return confirm('您確定要刪除嗎?');"  href='memberUpd.php?id=<?php echo $row[id]; ?>&get_act=del' class='btn '  title='刪除'><i class='fas fa-trash-alt  text-orange'></i></a>
											</td></tr>
											<tr>
											<td scope='row'>2020/04/30</td>
											<td scope='row'>
												<div>
													<b class='b-label'>排程1</b><span>&nbsp;13:30 ~ 15:30</span><br>
													<b class='b-label'>排程2</b><span>&nbsp;18:00 ~ 23:30</span>
												</div>
											</td>
											<td scope='row'>指定</td>
											<td scope='row' class='text-left'>
												<a href='refund-period-edit.php' class='btn ' title='編輯'><i class='fas fa-pencil-alt text-orange' ></i></a>
												<a onclick="return confirm('您確定要刪除嗎?');"  href='memberUpd.php?id=<?php echo $row[id]; ?>&get_act=del' class='btn '  title='刪除'><i class='fas fa-trash-alt  text-orange'></i></a>
											</td></tr>
											-->

					</tbody>
				</table>
			</div>
			<!-- 跳頁 上下頁-->
				<div class="container-fluid">
					<div class="text-center" id="dataTable_paginate">
						<?php
							if($rownum > $pagesize) {
								echo $pageurl;
								echo "".$lang->line("index.current_page")." $page | ".$lang->line("index.a_few_pages")." $pagenum ".$lang->line("index.page")."";
							}
						?>
					</div>
				</div>
			<!-- 跳頁 上下頁 end-->
		</div>			
</div>
<!--表格 END-->
</section>

<?php include('footer_layout.php'); ?>