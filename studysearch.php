<?php include('header_layout.php'); ?>                     
<?php include('nav.php'); ?>
<?php include('chk_log_in.php'); ?>
<?php if($user_sn) { ?> 
<section id="main" class="wrapper">
	<div class="rwd-box"></div><br>
	<div class="rwd-box"></div><br>        
	<div class="inner">  
		<div class="row">
			<div class="col-12"> 
				<div class="card"> 
				  <div class="member_header card-header"><?php echo $lang->line("index.student_info"); ?></div>  
				  <ul class="list-group list-group-flush">    
				    <li class="list-group-item"><b> 
						<?php echo $lang->line("index.name_username"); ?>：</b><br><?php echo $_SESSION['user']['cname']; ?> / <?php echo $_SESSION['user']['id']; ?>
						</li>
							<li class="list-group-item"><b>	
								<?php echo $lang->line("index.dormitory_number"); ?>：</b>  
								<?php 
									if($_SESSION['user']['room_id'] == 'C000'){ 
										echo '非住宿生'; 
									} else {
										echo $_SESSION['user']['room_id']; 
									}           
								?>  
						</li>
				    <li class="list-group-item"><b>
						<?php echo $lang->line("index.system_balance"); ?>：</b>
						<?php
							if($user_sn){
								echo round($rs['balance'])."/ NTD"; 
							} elseif($public_user_sn) {
								echo round($rs3['balance'])."/ NTD";
							}
						?>
					 </li>	
				  </ul>
				</div><!-- card end -->
			</div><!-- col-12 end --> 
		</div>  
    </div>
    <!-- 圖示智慧操作系統 -->
    <section class="ao-main">
        <div class="ao-container">  
            <div class="ao-box-main">
                <div class="ao-box"><a href="MemberEZCardRecordPublicList.php?betton_color=primary"><img src="images/Stored_icon.png" alt=""><h5><?php echo $lang->line("index.stored_value_query"); ?></h5></a></div>
                <div class="ao-box"><a href="MemberPowerRecordPublicList.php?betton_color=primary"><img src="images/Record_icon.png" alt=""><h5><?php echo $lang->line("index.electricity_bill_query"); ?></h5></a></div>
                <div class="ao-box"><a href="change_passowrd.php"><img src="images/Password_icon.png" alt=""><h5><?php echo $lang->line("index.password_change"); ?></h5></a></div>
                <div class="ao-box-after-box"></div>
            </div>
        </div>   
    </section> 
	<!-- End 圖示智慧操作系統 -->
	<div class="rwd-box"></div><br>
	<div class="rwd-box"></div><br>     
	<div class="rwd-box"></div><br>     
</section>
<?php } elseif($admin_id) { ?>
<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='smartstudyroom.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>
	
	<div class="rwd-box"></div><br>
	<div class="container" style="text-align: center;">
	<h1 class="jumbotron-heading">查詢</h1>
	</div>
	<div class="inner inner2">       
		<div class="row"> 
			<!--管理資訊 TABLE
			<div class="col-12">
				<div class="card">
				  <div class="member_header card-header">            
				  <?php if($_SESSION['admin_user']['id'] == 'andy'){ ?>
 					<?php echo '超級管理員'; ?>           
				  <?php } else { ?> <?php echo $lang->line("index.instructor_info"); ?> <?php } ?>
				  </div>
				  <ul class="list-group list-group-flush">    
				    <li class="list-group-item">                    
				    	<?php echo $lang->line("index.instructor_account"); ?>：<?php echo $_SESSION['admin_user']['id']; ?>
				    	<a href="admin_edit.php?id=<?php echo $_SESSION['admin_user']['sn']; ?>"><i class="fas fa-edit"></i></a>
				    </li>
				    <li class="list-group-item">
				    	<?php echo $lang->line("index.instructor_name"); ?>：<?php echo $_SESSION['admin_user']['cname']; ?>
				    </li>                      
					<?php if($_SESSION['admin_user']['id'] == 'andy'){ ?>
				    <li class="list-group-item"> 
				    	<?php echo $lang->line("index.data_backup"); ?>
				    	<br><a href="dump_db.php?act=all">SQL ALL Download</a> 
				    </li>
				     <li class="list-group-item"> 
				    	<?php echo '清空更新資料'; ?>
				    	<br><a href="truncate_data.php">data_updates_changes</a>
				    </li>        
					<?php } ?>
				  </ul>
				</div>
			</div> 
			-->
			
			<!-- 改成圖示 ICON-->
			<div class="col-lg-2 col-sm-6 offset-lg-1 p-4 text-center"> 
				<a  href="study-record.php">
				<img class="mb-3" src="img/15電力使用紀錄.png">
				<h4>電力使用紀錄</h4> </a> 
			</div>

			<div class="col-lg-2 col-sm-6 p-4 text-center"> 
				<a  href="study-stored.php">
				<img class="mb-3" src="img/01付款紀錄.png">
				<h4>付款紀錄查詢</h4> </a> 
			</div>
			<!--		
			<div class="col-lg-4 col-sm-6 p-4 text-center"> 
				<a  href="power-debtlist.php">
				<img class="mb-3" src="img/16負值餘額名單.png">
				<h4>負值餘額查詢</h4> </a> 
			</div>
			-->

			<div class="col-lg-2 col-sm-6 p-4 text-center"> 
				<a  href="study-nowsystem.php">
				<img class="mb-3" src="img/17系統使用現況.png">
				<h4>系統使用現況</h4> </a> 
			</div>
			<div class="col-lg-2 col-sm-6 p-4 text-center">
				<a  href="study-nowmeter.php">
				<img class="mb-3" src="img/18用電現況.png">
				<h4>用電現況</h4> </a> 
			</div>
			<div class="col-lg-2 col-sm-6 p-4 text-center"> 
				<a  href="study-report.php">
				<img class="mb-3" src="img/19報表匯出.png">
				<h4>報表匯出</h4> </a> 
			</div>

</section>
<?php } ?>
<?php include('footer_layout.php'); ?>

