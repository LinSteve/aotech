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
	<div class='col-12 btn-back'><a href='new-member2.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br>
	<div class="container" style="text-align: center;">
	<h1 class="jumbotron-heading">智慧電錶管理</h1>
	</div>
	<div class="inner">       
		<div class="row"> 
			
			<?php
			// 智慧電力管理 查詢 --
			if(check_access_icon(5)) {
			?>	
			<!--  有權限 ICON-->
			<div class="col-lg-6 col-sm-6 p-4 text-center"> 
				<a  href="powersearch.php">
				<img class="mb-3" src="img/13查詢.png">
				<h4>查詢</h4></a> <!--<p class="text-muted">付款紀錄</p>-->
			</div>			
			<?php
			} else {
			?>
			<!-- 無權限 ICON-->
			<div class="col-lg-6 col-sm-6 p-4 text-center"> 
				<a  href="#" onclick="return confirm('無權限！');">
					<img class="mb-3" src="img/13查詢.png">
				<h4>查詢</h4></a>
			</div>	
			<?php
			}
			?>

			<?php
			// 智慧電力管理 設定 --
			if(check_access_icon(6)) {
			?>	
			<!--  有權限 ICON-->
			<div class="col-lg-6 col-sm-6 p-4 text-center"> 
				<a  href="powersetting.php">
				<img class="mb-3" src="img/14管理設定.png">
				<h4>管理設定</h4></a> 
			</div>		
			<?php
			} else {
			?>
			<!-- 無權限 ICON-->
			<div class="col-lg-6 col-sm-6 p-4 text-center"> 
				<a  href="#" onclick="return confirm('無權限！');">
					<img class="mb-3" src="img/14管理設定.png">
				<h4>管理設定</h4></a>
			</div>	
			<?php
			}
			?>

			
		</div>
	</div>

</section>
<?php } ?>

<?php include('footer_layout.php'); ?>