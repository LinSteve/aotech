<?php

	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

 	$pagesize = 10;
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';

	$sql = "SELECT COUNT(*) as 'count' FROM ezcard_record WHERE 1 AND member_id = '{$user_sn}'";
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
	 
	$sql = "SELECT * FROM ezcard_record WHERE 1 AND member_id = '{$user_sn}' ORDER BY add_date DESC LIMIT ".($page-1)* $pagesize . ",".$pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();

	$sort_map = array('Stored' => '儲值', 'Refund' => '退費');
?>

<!--COPY 電力使用紀錄後端程式-->


<form action="MemberEZCardRecordPublicList.php" method="get">
<input type='hidden' name='betton_color' value='<?php echo $betton_color; ?>'>
<section id="main" class="wrapper">

	<div class='col-12 btn-back'>
		<a onclick="window.history.back();" href='#' >
			<i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label>
		</a>
	</div>
	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">付款紀錄</h1>
    </div>
	


<!--表格 -->

<div class='inner'>
	<div class="col-12">
					<br>
				  	<div class="table-responsive"><!-- bootstrap 修復 table 跑版 -->
						<table class="table  text-center font-weight-bold">
						  <thead class="thead-green">
						  <tr class="text-center">
							  <th scope="col">#</th>
							  <!--<th scope="col">日期</th>-->
							  <th scope="col"><?php echo $lang->line("index.saved_date"); ?></th> 
						      <th scope="col"><?php echo $lang->line("index.saved_value"); ?></th> 
							  <th scope="col"><?php echo $lang->line("index.state"); ?></th>
							  <!--
							  <th scope="col">開始用電時間</th>
							  <th scope="col">結束用電時間</th>
						      <th scope="col">開始度數 ~ 結束度數</th> 
							  <th scope="col">棟別/樓層</th>
							  <th scope="col"><?php echo $lang->line("index.room_number"); ?></th>
							  <th scope="col">學號/<?php echo $lang->line("index.member_name"); ?></th> 
						      <th scope="col">開始/結束用電時間</th>
						      <th scope="col">電費金額</th> 
							  <th scope="col">餘額</th>
							  -->


							  </tr>
 
						  </thead>
						  <tbody>
						    <?php 
								foreach($data as $row) 
								{
									$row_count = ($prepage * $pagesize) + ++$j;
									$show_date = date($date_format.' '.$time_format, strtotime($row["add_date"]));
									$show_fee  = $row["PayValue"];
									$show_sort = $sort_map[$row["Sort"]];
							?>
								<tr>
									<td scope='row'><?php echo $row_count ?></td>
									<td scope='row'><?php echo $show_date ?></td>
									<td scope='row'><?php echo $show_fee  ?></td>
									<td scope='row'><?php echo $show_sort ?></td>
							    </tr>
							<?php
								}
						    ?>
						  </tbody>
						</table>
					</div>

						<!-- 跳頁 上下頁-->
						<div class="row ">
							<div class="container-fluid text-center">
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














<!--OLD 表格-->
<!--
	<div class="inner">
		<div class="row">
				<div class="col-12">
						<table class="table">
						  <thead class="thead-dark">
						    <tr>
						      <th scope="col">#</th>
						      <th scope="col"><?php echo $lang->line("index.saved_date"); ?></th> 
						      <th scope="col"><?php echo $lang->line("index.saved_value"); ?></th> 
						      <th scope="col"><?php echo $lang->line("index.state"); ?></th>
						    </tr>     
						  </thead>
						  <tbody>
						  <?php     
									// $sql="select * from ezcard_record where 1 and CardID='".$GetIDcard."' limit " . ($page-1)* $pagesize . ",$pagesize ";
									// $rs=$PDOLink->Query($sql);
									// $rs->setFetchMode(PDO::FETCH_ASSOC); 
									// $i=0;
									// $j=1;
			
									// $langEnglishValue = $lang->line("index.if_if"); // value = en-us 
									// $DataTypes2 = array('Stored' => '付款','Refund' => '退費');
									// $OrderDataType = array(1 => '付款',2 => '使用空調');
									// $OrderStatusDataType = array("-" => '退費',"+" => '付款');
									// while($row=$rs->Fetch()){
									// $add_dates = date("Y-m-d H:i",strtotime($row[Time]));
									
							    // print "
								    // <tr>  
								      // <th scope='row'>".$j."</th>
								      // <td>".$add_dates."</td>
								      // <td>".PayValueZero($row[PayValue])." /NTD</td>
									  // <td>";
									   
								    // if($langEnglishValue == 'en-us'){
									   // print "
										// ".$row[Sort]." ";
									// } else {
									   // print "
										  // ".$DataTypes2[$row[Sort]]." ";
									// }
									 
								// print "	 </td>
								    // </tr>";
								// $j++;
							   // }
						   ?>
						  </tbody>
						</table>
				</div>
		</div>
		<?php  
	        // Echo $pageurl;
	        // Echo "<br /> ".$lang->line("index.current_page")." $page | ".$lang->line("index.a_few_pages")." $pagenum ".$lang->line("index.page")."
	        // ";
		?> 
	</div>

-->
<!--OLD 表格 END-->

</section>
<!--
<input type=hidden name=act>
<input type=hidden name=sn>
<input type=hidden name=edit_sn>
-->

</form>

<?php include('footer_layout.php'); ?>