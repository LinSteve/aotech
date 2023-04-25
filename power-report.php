<?php 

	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

	$pagesize  = 15;
	$opt_start = 2020;

	$stored    = "Stored";
	$refund    = "Refund";
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';
	
	$kw_start  = $_GET['kw_start'];
	$kw_end    = $_GET['kw_end'];
	$serach    = $_GET['serach'];
	
	// 給初值 -- 20200330
 	if($kw_start == "") { $kw_start = date('Y-m-d'); }
	if($kw_end   == "") { $kw_end   = date('Y-m-d'); }
	
	if($sel_year_start  == "") { $sel_year_start  = date('Y'); }
	if($sel_year_end    == "") { $sel_year_end    = date('Y'); }
	if($sel_month_start == "") { $sel_month_start = date('m'); }
	if($sel_month_end   == "") { $sel_month_end   = date('m'); }
	
	if($sel_year_all == "") { $sel_year_all = date('Y'); }
	
	$sql_kw    = "";
	
	$get_tab   = $_GET['get_tab'];
	
	if($get_tab == '') {
		$get_tab = 'day';
	}
?>
<!-- 教官查詢房號  -->  

<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='powersearch.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">報表匯出</h1>
    </div>
	
	<div class="row container-fluid mar-bot50 mar-center2">
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

	

<div class="inner inner2">
          <!--日/月/年分頁 標籤-->
	        <!-- tabs -->
    	    <div class="row">
	          <div class="container-fluid">
                    
                    <!-- Content Row Tab panes 查詢標籤切換頁-->
                    <div class="row h1-mar">
                      <!-- Content Column -->
                      <div class="col-lg-12 mb-4">
                        <!-- Approach -->
                        <div class=" card border-report mb-4">
							<!--TEST-->
							<ul class="nav nav-tabs  nav-border  nav-martop" role="tablist">
				        		<li class="nav-item col-lg-2 offset-lg-3"> 
                        			<a class="nav-link card active card-border" data-toggle="tab" id='tab_day' href="#day" role="tab" aria-selected="false">
                          			<span class="h5 mb-0 font-weight-bold ">日報表</span>
                        			</a>    
                      			</li>

				       			 <li class="nav-item col-lg-2">
                        			<a class="nav-link card  card-border" data-toggle="tab" id='tab_month' href="#month" role="tab" aria-selected="false">
                          			<span class="h5 mb-0 font-weight-bold ">月報表</span>
                        			</a>           
                    			</li>

				       			<li class="nav-item col-lg-2"> 
                        			<a class="nav-link card  card-border" data-toggle="tab" id='tab_year' href="#year" role="tab" aria-selected="false">
                          			<span class="h5 mb-0 font-weight-bold ">年報表</span>
                        			</a>
                      			</li>
                    		</ul>

                          <!--切換頁位置-->
                        <div class="card-body">
							<!--tab-content-->
                            <div class="tab-content tabcontent-border">
                            	<!--日報表-->
                            	<div class="tab-pane active show" id="day" role="tabpanel">
                                	<form id='mform1' action="power-report.php?get_tab=day" method="get">
										<input type='hidden' name='get_tab' value='day'>
										<div class='form-group row select-mar'>
							 				<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>開始時間</label>
							 				<div class='col-sm-9'> 
												<input class="form-control form-control2  " type="date" placeholder="開始時間：yyyy-mm-dd" size="20" name="kw_start" value="<?php echo $kw_start ?>">
							 				</div>
										</div>
							 

										<div class='form-group row'>
							 				<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right '>結束時間</label>
							 				<div class='col-sm-9'> 
								 				<input class="form-control   form-control2 " type="date" placeholder="結束時間：yyyy-mm-dd" size="20" name="kw_end" value="<?php echo $kw_end ?>">
							 				</div>
										</div>							
								  		<!--BTN 日報表查詢-->
								  		<br>
										<input type='hidden' name='serach' value='1'>
										<button type='submit' class='btn  btn-loginfont btn-primary2  col-4 offset-4'><?php echo $lang->line("index.confirm_query") ?></button>
										<br>
									</form>
	                            	<!--TABLE 日報表-->
									<div class="col-12" style="display:<?php echo ($serach == '1') ? 'block' : 'none'?>">
										<h1 class="jumbotron-heading text-center h1-mar"><?php echo ($serach == '1') ? $lang->line("serach_results") : "" ?></h1>

										<div class=" text-right ">
										<button type="button" class="btn btn-loginfont btn-primary2 col-sm-2 offset-sm-10 mb-3" onclick='export2excel1()'><?php echo $lang->line("index.export") ?></button>
										</div>
									<br>

				  						<div class="table-responsive">
											<table class="table  text-center font-weight-bold">
						 		 				<thead class="thead-green">
						  							<tr class="text-center">
							  							<th scope="col">#</th>
							  							<th scope="col">日期</th>
							  							<th scope="col">卡號/姓名</th>
							  							<th scope="col">儲值金額</th>
							  							<th scope="col">退費金額</th>
							  							<th scope="col">小計</th> 
						      							<th scope="col">備註</th> 
											  		</tr>
										  		</thead>
										  
						  						<tbody>
						    					<?php 
													$j = 0;
													
													if($kw_start) {
														$s_date = date('Y-m-d H:i:s', strtotime($kw_start));
														$sql_kw.= " AND e.add_date >= '{$s_date}' ";
													}
													
													if($kw_end) {
														$e_date = date('Y-m-d H:i:s', strtotime($kw_end.'+1 day'));
														$sql_kw.= " AND e.add_date < '{$e_date}' ";
													}											
													
													$sql = "SELECT e.*, m.cname, e.add_date as 'day' 
															FROM `ezcard_record` e LEFT JOIN `member` m ON m.id = e.member_id 
															WHERE 1 {$sql_kw} ORDER BY e.add_date DESC";
													$rs = $PDOLink->Query($sql);
													$rs_tmp = $rs->fetchAll();
													
													foreach($rs_tmp as $v) {
														
														$day = $v['day'];
														$pos = $v['Sort'];
														$amt = $v['PayValue'];
														
														$nmn = $v['CardID'];
														$nmn.= " / ";
														$nmn.= $v['cname'];
														
														$rs_data1[$day]['nmn'] = $nmn;

														if($pos == $stored) {
															$rs_data1[$day]['amt'] += $amt;
														}
														
														if($pos == $refund) {
															$rs_data1[$day]['ref'] += $amt;
														}
													}
													
													if(isset($_GET['page'])) {
														$page = $_GET['page'];
													} else {
														$page = 1;
													}
													
													foreach($rs_data1 as $d => $row)
													{
														$j++;
														
														$amt = $row['amt'] == '' ? 0 : $row['amt'];
														$ref = $row['ref'] == '' ? 0 : $row['ref'];
														$sum = $amt - $ref;
														$b_style  = ($sum < 0) ? "text_negative" : "text_normal";
														$nmn = $row['nmn'];
														$shw = date($date_format.' '.$time_format, strtotime($d));
														
														if( ($j > (($page-1) * $pagesize)) & ($j <= ($page) * $pagesize) ) 
														{	
															print " <tr>
																		<th scope='row'>".$j."</th>
																		<td>{$shw}</td>
																		<td>{$nmn}</td>
																		<td>{$amt}</td>
																		<td>{$ref}</td>
																		<td class= '$b_style'>{$sum}</td>
																		<td></td>
																	</tr>";
														}
													}
													
													$rownum = $j;
						    					?>
						  						</tbody>
											</table>
										</div>


									<!-- 跳頁 上下頁-->
									<div class="row ">
										<div class="container-fluid">
											<div class="text-center" id="dataTable_paginate">
											  <?php
											  
												$pagenum  = (int) ceil($rownum / $pagesize);  
												$prepage  = $page - 1;                        
												$nextpage = $page + 1;                        
												$pageurl  = '';
											
												if($page == 1) {                         
													$pageurl.=" ".$lang->line("index.home")." | ".$lang->line("index.previous_page")." | ";
												} else {
													$pageurl.="<a href=\"?cardnum={$cardnum}&sel_build={$sel_build}&sel_level={$sel_level}&sel_dev={$sel_dev}&kw_start={$kw_start}&kw_end={$kw_end}&get_tab={$get_tab}&serach={$serach}&page=1\">".$lang->line("index.home")."</a> | 
															   <a href=\"?cardnum={$cardnum}&sel_build={$sel_build}&sel_level={$sel_level}&sel_dev={$sel_dev}&kw_start={$kw_start}&kw_end={$kw_end}&get_tab={$get_tab}&serach={$serach}&page=$prepage\">".$lang->line("index.previous_page")."</a> | ";
												}

												if($page==$pagenum || $pagenum==0) {     
													$pageurl.=" ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
												} else {
													$pageurl.="<a href=\"?cardnum={$cardnum}&sel_build={$sel_build}&sel_level={$sel_level}&sel_dev={$sel_dev}&kw_start={$kw_start}&kw_end={$kw_end}&get_tab={$get_tab}&serach={$serach}&page=$nextpage\">".$lang->line("index.next_page")."</a> | 
															   <a href=\"?cardnum={$cardnum}&sel_build={$sel_build}&sel_level={$sel_level}&sel_dev={$sel_dev}&kw_start={$kw_start}&kw_end={$kw_end}&get_tab={$get_tab}&serach={$serach}&page=$pagenum\">".$lang->line("index.last_page")."</a>";
												}											  
											  
												if($rownum > $pagesize) {
													echo $pageurl;
													echo " ".$lang->line("index.current_page")." $page | ".$lang->line("index.a_few_pages")." $pagenum ".$lang->line("index.page")."";
												}
												
												$rownum = 0; // inital
											  ?>
											</div>
										</div>
									</div>
									<!-- 跳頁 上下頁 END -->


									</div>
                            		<!--TABLE 日報表 END-->
								</div>
                            	<!--日報表 END-->
							

<?php
										if($get_tab == 'month') 
										{
											
											$rs_data2;
											
											$sql_kw = "";
											
											$sel_year_start  = $_GET['sel_year_start'];
											$sel_year_end    = $_GET['sel_year_end'];
											$sel_month_start = $_GET['sel_month_start'];
											$sel_month_end   = $_GET['sel_month_end'];
											
											if($sel_year_start != '' & $sel_month_start != '') {
												
												$qry_date = date('Y-m-d H:i:s', strtotime("{$sel_year_start}-{$sel_month_start}-01 00:00:00"));
												
												$sql_kw .= " AND add_date >= '{$qry_date}' ";
												
											} else {

												if($sel_year_start != '') {
													$sql_kw .= " AND YEAR(add_date) >= {$sel_year_start} ";
												} 
												
												if($sel_month_start != '') {
													$sql_kw .= " AND MONTH(add_date) >= {$sel_month_start} ";
												}												
											}
												
											if($sel_year_end != '' & $sel_month_end != '') {
												
												$qry_date = date('Y-m-d H:i:s', strtotime("{$sel_year_end}-{$sel_month_end}-01 00:00:00 +1 month"));
												
												$sql_kw .= " AND add_date < '{$qry_date}' ";
												
											} else {
												if($sel_year_end != '') {
													$sql_kw .= " AND YEAR(add_date) <= {$sel_year_end} ";
												} 
												
												if($sel_month_end != '') {
													$sql_kw .= " AND MONTH(add_date) <= {$sel_month_end} ";
												}												
											}
											
											$sql = "SELECT YEAR(add_date) as 'year', MONTH(add_date) as 'month', 
													DAY(add_date) as 'day', SUM(PayValue) as 'amount', Sort 
													FROM `ezcard_record` WHERE 1 {$sql_kw} GROUP BY YEAR(add_date), 
													MONTH(add_date), DAY(add_date), Sort ORDER BY add_date";
											$rs  = $PDOLink->Query($sql);
											$rs_tmp = $rs->fetchAll();
											
											foreach($rs_tmp as $v) {
												
												$yy  = $v['year'];
												$mm  = $v['month'];
												$dd  = $v['day'];
												$pos = $v['Sort'];
												$amt = $v['amount'];

												if($pos == $stored) {
													$rs_data2[$yy][$mm][$dd]['amt'] += $amt;
												}
												
												if($pos == $refund) {
													$rs_data2[$yy][$mm][$dd]['ref'] += $amt;
												}
											}
										}
?>
								<!--月報表-->	  
								<div class="tab-pane pad" id="month" role="tabpanel">
                            	<!--月報表查詢選項-->

								<form id='mform2' action="power-report.php?get_tab=month" method="get">
									<input type='hidden' name='get_tab' value='month'>
									<div class='col-12'>
										<section class='panel panel-noshadow'>
	             							<div class='panel-body'>
											 	<h4 class="mb-0 font-weight-bold text-center">選擇查詢的開始年/月份</h4>

												<div class="form-group  row">
													<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right btn-martop20">年份</label>	
													<div class="col-sm-9  form-inline ">
														<select class="room_changes col form-control  selectpicker show-tick" title='請選擇'  size="1" name="sel_year_start"  >
														<?php
															for($i=date('Y'); $i>=$opt_start; $i--) { 
																echo "<option value='{$i}' ". (($i == $sel_year_start) ? "selected" : "") .">{$i}</option>"; 
															}
														?>
														</select>
													</div>
												</div>
												<div class="form-group row ">
													<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right btn-martop20">月份</label>	
													<div class="col-sm-9  form-inline">
														<select class="room_changes col form-control  selectpicker show-tick" title='請選擇'  size="1" name="sel_month_start"  >
														<?php
															for($i=1; $i<=12; $i++) { 
																echo "<option value='{$i}' ". (($i == $sel_month_start) ? "selected" : "") .">{$i} 月</option>"; 
															} 
														?>
														</select>
													</div>
												</div>										

												
											 	<h4 class="mb-0 font-weight-bold h1-mar text-center">選擇查詢的結束年/月份</h4>

												<div class="form-group  row ">
													<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right btn-martop20">年份</label>	
													<div class="col-sm-9  form-inline ">
														<select class="room_changes col form-control  selectpicker show-tick" title='請選擇'  size="1" name="sel_year_end"  >
														<?php
															for($i=date('Y'); $i>=$opt_start; $i--) { 
																echo "<option value='{$i}' ". (($i == $sel_year_end) ? "selected" : "") .">{$i}</option>"; 
															}
														?>
														</select>
													</div>
												</div>
												<div class="form-group row ">
													<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right btn-martop20">月份</label>	
													<div class="col-sm-9  form-inline">
														<select class="room_changes col form-control  selectpicker show-tick" title='請選擇'  size="1" name="sel_month_end"  >
														<?php
															for($i=1; $i<=12; $i++) { 
																echo "<option value='{$i}' ". (($i == $sel_month_end) ? "selected" : "") .">{$i} 月</option>"; 
															} 
														?>
														</select>
													</div>
												</div>	

												<br><br>
												<input type='hidden' name='serach' value='2'>
												<button type='submit' class='btn  btn-loginfont btn-primary2  col-4 offset-4'><?php echo $lang->line("index.confirm_query") ?></button>
	             							</div>
	             						</section>
	             					</div>
								</form>
                            	<!--月報表查詢選項 END-->

	                            	<!--TABLE 月報表-->
									<div class="col-12" style="display:<?php echo ($serach == '2') ? 'block' : 'none'?>">
										<h1 class="jumbotron-heading text-center h1-mar"><?php echo ($serach == '2') ? $lang->line("serach_results") : "" ?></h1>

										<div class=" text-right ">
										<button type="button" onclick='export2excel2()' class="btn btn-loginfont btn-primary2 col-sm-2 offset-sm-10 mb-3"><?php echo $lang->line("index.export") ?></button>
										</div>
										<br>

				  						<div class="table-responsive">
											<table class="table  text-center font-weight-bold">
						 		 				<thead class="thead-green">
						  							<tr class="text-center">
							  							<th scope="col">#</th>
							  							<th scope="col">日期</th>
							  							<th scope="col">儲值總金額</th>
							  							<th scope="col">退費總金額</th>
							  							<th scope="col">小計</th> 
						      							<th scope="col">備註</th> 
											  		</tr>
										  		</thead>
										  
						  						<tbody>
<?php 
												if($get_tab == 'month') 
												{
													$j = 0;

													if(isset($_GET['page'])) {               
														$page = $_GET['page'];  
													} else {
														$page = 1;                                 
													}
							
													foreach($rs_data2 as $y => $outer)
													{
														foreach($outer as $m => $inner) 
														{
															foreach($inner as $d => $row) 
															{
																$j++;
																
																$shw = date($date_format, strtotime($y.'-'.$m.'-'.$d));
																$amt = $row['amt'] == '' ? 0 : $row['amt'];
																$ref = $row['ref'] == '' ? 0 : $row['ref'];
																$sum = $amt - $ref;
																$b_style  = ($sum < 0) ? "text_negative" : "text_normal";
																
																if( ($j > (($page-1) * $pagesize)) & ($j <= ($page) * $pagesize) ) 
																{	
																	echo "<tr>
																			<th scope='row'>".$j."</th>
																			<td>{$shw}</td>
																			<td>{$amt}</td>
																			<td>{$ref}</td>
																			<td class= '$b_style'>{$sum}</td>
																			<td></td>
																		  </tr>";													
																}
															}
														}
													}
													
													$rownum = $j;
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
												$pagenum  = (int) ceil($rownum / $pagesize);  
												$prepage  = $page - 1;                        
												$nextpage = $page + 1;                        
												$pageurl  = '';

												if($page == 1) {                         
													$pageurl.=" ".$lang->line("index.home")." | ".$lang->line("index.previous_page")." | ";
												} else {
													$pageurl.="<a href=\"?get_tab=month&sel_year_start={$sel_year_start}&sel_month_start={$sel_month_start}&sel_year_end={$sel_year_end}&sel_month_end={$sel_month_end}&serach={$serach}&page=1\">".$lang->line("index.home")."</a> | 
															   <a href=\"?get_tab=month&sel_year_start={$sel_year_start}&sel_month_start={$sel_month_start}&sel_year_end={$sel_year_end}&sel_month_end={$sel_month_end}&serach={$serach}&page=$prepage\">".$lang->line("index.previous_page")."</a> | ";
												}

												if($page==$pagenum || $pagenum==0) {     
													$pageurl.=" ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
												} else {
													$pageurl.="<a href=\"?get_tab=month&sel_year_start={$sel_year_start}&sel_month_start={$sel_month_start}&sel_year_end={$sel_year_end}&sel_month_end={$sel_month_end}&serach={$serach}&page=$nextpage\">".$lang->line("index.next_page")."</a> | 
															   <a href=\"?get_tab=month&sel_year_start={$sel_year_start}&sel_month_start={$sel_month_start}&sel_year_end={$sel_year_end}&sel_month_end={$sel_month_end}&serach={$serach}&page=$pagenum\">".$lang->line("index.last_page")."</a>";
												}
												
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
									<!--TABLE 月報表 END-->	  
								</div>
								<!--月報表 END-->

<?php
										if($get_tab == 'year') 
										{
											
											$rs_data3;
											
											$sql_kw = "";
											
											$sel_year_all  = $_GET['sel_year_all'];
											
											if($sel_year_all != '') {
												$sql_kw = " AND YEAR(add_date) = '{$sel_year_all}' ";
											}										
											
											$sql = "SELECT YEAR(add_date) as 'year', MONTH(add_date) as 'month', 
													SUM(PayValue) as 'amount', Sort 
													FROM `ezcard_record` WHERE 1 {$sql_kw} GROUP BY YEAR(add_date), 
													MONTH(add_date), Sort ORDER BY add_date";
											$rs  = $PDOLink->Query($sql);
											$rs_tmp = $rs->fetchAll();
											
											foreach($rs_tmp as $v) {
												
												$yy  = $v['year'];
												$mm  = $v['month'];
												$pos = $v['Sort'];
												$amt = $v['amount'];
												
												if($pos == $stored) {
													$rs_data3[$yy][$mm]['amt'] += $amt;
												}
												
												if($pos == $refund) {
													$rs_data3[$yy][$mm]['ref'] += $amt;
												}
											}
										}
?>
								<!--年報表-->	  
								<div class="tab-pane pad" id="year" role="tabpanel">
                            	<!--年報表查詢選項-->

								<form id='mform3' action="power-report.php?get_tab=year" method="get">
									<input type='hidden' name='get_tab' value='year'>
									<div class='col-12'>
										<section class='panel panel-noshadow'>
	             							<div class='panel-body'>
											 	<h4 class="mb-0 font-weight-bold text-center">選擇查詢年份</h4>

												<div class="form-group  row">
													<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right btn-martop20">年份</label>	
													<div class="col-sm-9  form-inline ">
														<select class="room_changes col form-control  selectpicker show-tick" title='請選擇'  size="1" name="sel_year_all"  >
														<?php
															for($i=date('Y'); $i>=$opt_start; $i--) { 
																echo "<option value='{$i}' ". (($i == $sel_year_all) ? "selected" : "") .">{$i}</option>"; 
															}
														?>
														</select>
													</div>
												</div>

												<br><br>
												<input type='hidden' name='serach' value='3'>
												<button type='submit' class='btn  btn-loginfont btn-primary2  col-4 offset-4'><?php echo $lang->line("index.confirm_query") ?></button>
	             							</div>
	             						</section>
	             					</div>
								</form>
                            	<!--年報表查詢選項 END-->

	                            	<!--TABLE 年報表-->
									<div class="col-12" style="display:<?php echo ($serach == '3') ? 'block' : 'none'?>">
										<h1 class="jumbotron-heading text-center h1-mar"><?php echo ($serach == '3') ? $lang->line("serach_results") : "" ?></h1>

										<div class=" text-right ">
										<button type="button" onclick='export2excel3()' class="btn btn-loginfont btn-primary2 col-sm-2 offset-sm-10 mb-3"><?php echo $lang->line("index.export") ?></button>
										</div>
										<br>

				  						<div class="table-responsive">
											<table class="table  text-center font-weight-bold">
						 		 				<thead class="thead-green">
						  							<tr class="text-center">
							  							<th scope="col">#</th>
							  							<th scope="col">月份</th>
							  							<th scope="col">儲值總金額</th>
							  							<th scope="col">退費總金額</th>
							  							<th scope="col">小計</th> 
						      							<th scope="col">備註</th> 
											  		</tr>
										  		</thead>
										  
						  						<tbody>
<?php
											$j = 0;
											
											foreach($rs_data3 as $y => $row)
											{
												foreach($row as $m => $v) 
												{
													$amt = $v['amt'] == '' ? 0 : $v['amt'];
													$ref = $v['ref'] == '' ? 0 : $v['ref'];
													$sum = $amt - $ref;
													$b_style  = ($sum < 0) ? "text_negative" : "text_normal";
													$shw = str_pad($y,2,"0",STR_PAD_LEFT)."/".str_pad($m,2,"0",STR_PAD_LEFT);
													
													echo "<tr>
															<th scope='row'>".++$j."</th>
															<td>{$shw}</td>
															<td>{$amt}</td>
															<td>{$ref}</td>
															<td class= '$b_style'>{$sum}</td>
															<td></td>
														  </tr>";
												}
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
									<!--TABLE 年報表 END-->	  
								</div>
								<!--年報表 END-->


							</div>
							<!--tab-content END-->
                        </div>
						<!--切換頁位置 END-->
						  




                        </div>
                      </div>
                    </div>
                    

	          </div><!--container-fluid-->
    	    </div>
	        <!-- 日/月/年分頁 標籤 END -->
          </div>


</section>


<script>

$(document).ready(function() {
	
	var get_tab = '<?php echo $get_tab ?>';
	
	switch(get_tab) {
		
		case 'day':
		
			$('#day').addClass('active');
			$('#month').removeClass('active');
			$('#year').removeClass('active');
			
			$('#tab_day').click();
		
			break;
		case 'month':
			
			$('#tab_month').click();
		
			break;
		case 'year':
			
			$('#tab_year').click();
			
			break;
		default:
			
			$('#tab_day').click();
			
			break;
	}
	
});

function export2excel1() {
	
	$('#mform1').prop('action', 'model/excelmember_report_save1.php');
	$('#mform1').prop('method', 'get');
	
	$('#mform1').submit();
	
	$('#mform1').prop('action', 'power-report.php');
	return false;
}

function export2excel2() {
	
	$('#mform2').prop('action', 'model/excelmember_report_save2.php');
	$('#mform2').prop('method', 'get');
	
	$('#mform2').submit();
	
	$('#mform2').prop('action', 'power-report.php');
	return false;
}

function export2excel3() {
	
	$('#mform3').prop('action', 'model/excelmember_report_save3.php');
	$('#mform3').prop('method', 'get');
	
	$('#mform3').submit();
	
	$('#mform3').prop('action', 'power-report.php');
	return false;
}


</script>

<?php include('footer_layout.php'); ?>