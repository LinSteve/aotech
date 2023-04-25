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
	<div class='col-12 btn-back'><a href='smartpower.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>
	
	<div class="rwd-box"></div><br>
	<div class="container" style="text-align: center;">
	<h1 class="jumbotron-heading">管理設定</h1>
	</div>
	<div class="inner inner2">       
		<div class="row"> 
			
			<?php
			// 費率設定 僅最高管理
			if(check_access_icon(7)) {
			?>	
			<div class="col-lg-2 col-sm-6 offset-lg-1 p-4 text-center"> 
				<a  href="rate.php">
				<img class=" mb-3 " src="img/20費率設定.png">
				<h4>費率設定</h4> </a> 
			</div>		
			<?php
			} else {
			?>
			<div class="col-lg-2 col-sm-6 offset-lg-1  p-4 text-center"> 
				<a  href="#" onclick="return confirm('無權限！');">
				<!--<i class="lockicon fas fa-lock "></i>-->
				<img class=" mb-3 " src="img/20費率設定.png">
				<h4>費率設定</h4> </a> 
			</div>
			<?php
			}
			?>

			<?php
			// 收費設定 僅最高管理
			if(check_access_icon(8)) {
			?>	
			<div class="col-lg-2 col-sm-6 p-4 text-center"> 
				<a  href="charge-system.php">
				<img class=" mb-3 " src="img/21收費設定.png">
				<h4><?php echo $lang->line("index.charge_setting"); ?></h4> </a> 
			</div>	
			<?php
			} else {
			?>
			<div class="col-lg-2 col-sm-6 p-4 text-center"> 
				<a  href="#" onclick="return confirm('無權限！');">
					<img class=" mb-3 " src="img/21收費設定.png">					
				<h4><?php echo $lang->line("index.charge_setting"); ?></h4> </a> 
			</div>
			<?php
			}
			?>
			
			<?php
			// 收費模式設定
			if(check_access_icon(9)) {
			?>	
			<div class="col-lg-2 col-sm-6 p-4 text-center">
				<a  href="charge-mode.php">
				<img class=" mb-3 " src="img/22收費模式設定.png">
				<h4>收費模式設定</h4> </a> 
			</div>
			<?php
			} else {
			?>
			<div class="col-lg-2 col-sm-6 p-4 text-center">
				<a  href="#" onclick="return confirm('無權限！');">
					<img class=" mb-3 " src="img/22收費模式設定.png">
				<h4>收費模式設定</h4> </a> 
			</div>
			<?php
			}
			?>
			
			<?php
			// 退費時段設定 
			if(check_access_icon(10)) {
			?>	
			<div class="col-lg-2 col-sm-6 p-4 text-center"> 
				<a  href="refund-period.php">
				<img class=" mb-3 " src="img/23退費時段設定.png">
				<h4>退費時段設定</h4> </a> 
			</div>
			<?php
			} else {
			?>
			<div class="col-lg-2 col-sm-6 p-4 text-center"> 
				<a  href="#" onclick="return confirm('無權限！');">
					<img class=" mb-3 " src="img/23退費時段設定.png">
				<h4>退費時段設定</h4> </a> 
			</div>
			<?php
			}
			?>
			
			<?php
			// 期末退費設定
			if(check_access_icon(11)) {
			?>	
			<div class="col-lg-2 col-sm-6 p-4 text-center"> 
				<a  href="refund-final.php">
				<img class=" mb-3 " src="img/24期末退費設定.png">
				<h4>期末退費設定</h4> </a> 
			</div>	
			<?php
			} else {
			?>
			<div class="col-lg-2 col-sm-6 p-4 text-center"> 
				<a  href="#" onclick="return confirm('無權限！');">
					<img class=" mb-3 " src="img/24期末退費設定.png">
				<h4>期末退費設定</h4> </a> 
			</div>
			<?php
			}
			?>
			
			<?php
			// 公告&通知設定
			if(check_access_icon(12)) {
			?>	
			<!--  有權限 ICON-
			<div class="col-lg-4 col-sm-6 p-4 text-center">
				<a  href="news.php">
				<img class=" mb-3 " src="img/25公告&通知設定.png">
				<h4>公告事項</h4> </a> 
			</div>
			-->
			<?php
			} else {
			?>
			<!-- 無權限 ICON
			<div class="col-lg-4 col-sm-6 p-4 text-center">
				<a  href="#" onclick="return confirm('無權限！');">
					<img class=" mb-3 " src="img/25公告&通知設定.png">
				<h4>公告事項</h4> </a> 
			</div>
			-->
			<?php
			}
			?>
			
		</div>
	</div>

</section>
<?php } ?>
<?php include('footer_layout.php'); ?>