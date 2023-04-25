<?php 

	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

	if($user_sn) {

		$sql = "SELECT * FROM `member` WHERE id = '{$user_sn}'";
		$rs  = $PDOLink->prepare($sql);
		$rs->execute();
		$tmp = $rs->fetch();
		$room_num = $tmp["room_strings"];
		$balance  = round($tmp["balance"], 1);
		$b_style  = ($balance < 0) ? "text_negative" : "text-green";
?> 
<section id="main" class="wrapper" style="padding-bottom:0;">
	<div class="rwd-box"></div><br>
	<div class="container" style="text-align: center;">
		<h1 class="mb-2">住宿中心</h1>
	</div>
	<!-- 指定控電提示文字 -->
	<div class="row container-fluid mar-bot50">
		<?php if($_GET['success'] == 1){ ?>
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
			<strong>設定完成！</strong>
			</div>
		<?php } elseif($_GET['error'] == 1){ ?> 
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
			<strong>設定失敗！</strong>
			</div>	
		<?php } ?>
	</div>  
<div class="inner">  
	<div class="row">
		<!-- 指定控電製作說明 -->
		<div class="col-12 alert alert-orange d-none">
			<p class="text-lg font-weight-bold NG-color">【製作說明】</p>
			<p class="text-lg font-weight-bold NG-color">
			1.指定控電：學生登入時，可自己設定控制用電，製作時可參考【後台】指定扣款設定<br>
			2.大原則：<br>
				2.1住宿生只能開啟自己房間的電，生成的指令中的id，為該住宿生的member.id<br>
				2.2若住宿生餘額<=0，不可執行指定控電<br>
				2.3公用卡比學生登入後的控制用電，比照以上機制製作<br>
				2.4若身分是非住宿生(房號為空)，則學生登入後，不可執行指定控電<br>
				2.5設定成功時，會產生Log紀錄<br>
			3.硬體指令(不用異動)：指定房間控電權命令，id帶出member_id，如:{"op":"PowerOn","table":"member","id":"95"}<br>
			4.<a href='#' role='button' class='btn btn-warning btn-icon-split text-lg font-weight-bold text-white p-0' onclick='setElectricCtrl($user_sn)'>
					<span class='icon '>
						<i class='fas fa-bolt'></i>
					</span>
					<span class='text'>指定控電</span>
				</a>：對應2.1，當住宿生有餘額時，會顯示本按鈕<br>
			5.<a href='#' role='button' class='btn bg-gray-400 btn-icon-split text-lg font-weight-bold text-white p-0' onclick='NosetElectricCtrl($user_sn)'>
					<span class='icon'>
						<i class='fas fa-bolt'></i>
					</span>
					<span class='text'>餘額不足無法控電</span>
				</a>：對應2.2，當住宿生餘額<=0，不可執行指定控電<br>
			6.<a href='#!' role='alert' class='btn bg-gray-400 btn-icon-split text-lg font-weight-bold text-white p-0'>
				<span class='icon'>
					<i class='fas fa-bolt'></i>
				</span>
				<span class='text'>非住宿生無法控電</span>
			  </a>：對應2.4，非住宿生，不可操作指定控電
			</p>
		</div>
		<!-- 指定控電製作說明 END-->
		<div class="col-lg-8 col-md-offset-2 col-sm-offset-0"> 
				<div class="mb-2">

				<div class="mb-2 d-none">
						<?php
							if($room_num == '') {
								echo "
								<a href='#!' role='alert' class='btn bg-gray-400 btn-icon-split text-lg font-weight-bold text-white p-0'>
									<span class='icon'>
										<i class='fas fa-bolt'></i>
									</span>
									<span class='text'>非住宿生無法控電</span>
								</a>
								";
							} else {
								if($balance > 0) {
									echo "
										<a href='#!' role='button' class='btn btn-warning btn-icon-split text-lg font-weight-bold text-white p-0' onclick='setElectricCtrl($user_sn)'>
											<span class='icon '>
												<i class='fas fa-bolt'></i>
											</span>
											<span class='text'>指定控電</span>
										</a>
									";
								} else {
									echo "
									<a href='#!' role='button' class='btn bg-gray-400 btn-icon-split text-lg font-weight-bold text-white p-0' onclick='NosetElectricCtrl($user_sn)'>
										<span class='icon'>
											<i class='fas fa-bolt'></i>
										</span>
										<span class='text'>餘額不足無法控電</span>
									</a>
									";
								}
							}
						?>
				</div>
				<div class="card shadow  mb-4">

						<div class="card-header py-3">
						<h6 class="m-0 font-weight-bold text-center"><?php echo $lang->line("index.student_info"); ?></h6>
						</div>
						<ul class="list-group list-group-flush ">    
							<li class="list-group-item">
								<i class="fa fa-user h4 "></i>&nbsp;
									<span class="h4 text-green "><?php echo $lang->line("index.name_username"); ?>：</span>
									<span class="h4 text-green">
										<br class='visible-xs-block'>
										<?php  echo $_SESSION['user']['cname'].'/'.$_SESSION['user']['username']; ?>
									</span>
							</li>

							<li class="list-group-item ">
									<i class="fa fa-home h4 "></i>&nbsp;
										<span class="h4 text-green "><?php echo $lang->line("index.dormitory_number"); ?>：</span>
										<span class="h4 text-green"><?php echo $room_num ?></span>
							</li>
			
							<li class="list-group-item ">&nbsp;
								<i class="fas fa-dollar h4 "></i>
								<span class="h4 text-green ">
									&nbsp;<?php echo $lang->line("index.system_balance"); ?>：
								</span>

								<span class="h4 <?php echo $b_style?>">
								<?php echo $balance ?> <span class='text-green'>/ NTD</span>
								</span>
							</li>
						</ul>

				</div><!-- card end -->
		</div><!-- col-8 end --> 
				
	</div>  
 </div>
	<!-- 住宿中心圖示智慧操作系統 -->
		<div class="inner6">       
			<div class="row mar-center">

				<div class="col-lg-6 col-sm-6 p-4 text-center"> 
					<a  href="record-stored.php">
					<img class="mb-3" src="img/01付款紀錄.png">
					<h4>付款紀錄</h4> </a>
				</div>

				<div class="col-lg-6 col-sm-6 p-4 text-center"> 
					<a  href="record-poweruser.php">
					<img class="mb-3" src="img/15電力使用紀錄.png">
					<h4>電力使用紀錄</h4> </a> 
				</div>

			</div>
		</div>
	<!-- End 住宿中心圖示智慧操作系統 -->
</section>

<?php } elseif($admin_id) { ?>
<section id="main" class="wrapper">
	<div class="rwd-box"></div><br><br><br>
	<div class="container" style="text-align: center;">
	<h1 class=" jumbotron-heading">管理中心</h1>
	</div>
	<div class="container">       
		<div class="row justify-content-center">
			<div class="col-lg-4 col-sm-6 col-xs-12 p-4 text-center"> 
				<a  href="roomlist-manager.php">
				<img class="mb-3" src="img/01宿舍名單群組管理.png">
				<h4>宿舍名單管理</h4> </a>
			</div>
			<div class="col-lg-4 col-sm-6 col-xs-12 p-4 text-center">
				<a  href="smartpower.php">
				<img class="mb-3" src="img/03智慧電力管理.png">
				<h4>智慧電錶管理</h4> </a> 
			</div>
		</div>
	</div>
	
</section>
<?php } ?>
<?php include('footer_layout.php'); ?>
<script>
	function setElectricCtrl(id){
			if(confirm('此動作將取代原先控電人員\n是否繼續開通控電?\n (1房間僅限1人有控電權)')) {
			location.replace("model/electric_ctrl_member.php?id=" + id);
		}
		return;
	}
	function NosetElectricCtrl(id){
		alert('餘額不足無法控電');
		return;
	}
</script>
