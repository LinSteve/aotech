<?php 

	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');
	
	$room_id   = '';
	$price     = '';
	$room_num  = $_GET["room_numbers_kw"];
	$price_all = $_GET["price_elec_degree"];
	
	if($room_num) 
	{	
		$sql = "SELECT * FROM `room` WHERE `name` = ?";
		$tmp = func::excSQLwithParam('select', $sql, array($room_num), false, $PDOLink); 
		$room_id = $tmp['id'];
		$price = $tmp['price_degree'];
	}
	
	if(!isset($price_all)) {
		
		$sql   = "SELECT * FROM `room` LIMIT 0, 1";
		$rs    = $PDOLink->prepare($sql);
		$rs->execute();
		$tmp   = $rs->fetch();
		
		$price_all = $tmp['price_degree'];
	}
?>



<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='powersetting.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>
	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">費率設定</h1>
	</div>

	<div class="row container-fluid mar-bot50 mar-center2">
		<?php if($_GET['success'] == 1){ ?>
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
			<strong><?php echo $lang->line("index.success_room_settings"); ?>!!</strong>
			</div>
		<?php } elseif($_GET['success'] == 2) { ?>
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
			<strong><?php echo $lang->line("index.success_room_settings_all"); ?>!!</strong>
			</div>
		<?php } elseif($_GET['error'] == 1) { ?> 
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
			<strong>Error</strong>資料輸入錯誤或不存在
			</div>	
		<?php } elseif($_GET['error'] == 2) { ?> 
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
			請輸入用電度數
			</div>					
		<?php } ?>
	</div>    


	<!--費率設定-->
	<div class="inner">   
		<div class="row justify-content-center">
			<!-- 個別房間設定_現在改為110電燈插座_電錶設定 -->
			<div class="col-lg-6">
				<div class="card shadow mb-4">

					<div class="card-header py-3">
						<h6 class="m-0 font-weight-bold text-center">
							<?php echo $lang->line('110V_meter').'<br>'.
										$lang->line('rate_light_socket')
							?>
						</h6>
					</div>
					<div class="card-body ">					
						<div class="form-group">
							<form action="" method="get">
								<label class="label-center col btn-marbot20">輸入房號</label>  
								<input type="search" name="room_numbers_kw" value="<?php echo $room_num ?>" class=" form-control  col-8 offset-2" placeholder="Ex. T201" required>
								<button class="btn btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-6 offset-3">查詢</button>
							</form>
						</div>
						
						<div class="form-group  btn-martop30">
						<?php
						if($room_id != '') {
						?>
							<form id="myForm2" action="model/rate_upd.php" method="post">
								<input type='hidden' name='room_id'  value='<?php echo $room_id ?>'>
								<input type='hidden' name='room_num' value='<?php echo $room_num ?>'>
								<label class="label-center col btn-marbot20">用電度數</label>
								<input step="0.1" min="0" max="25.5" type="number" required="required" class="form-control col-8 offset-2" name="price_elec_degree" value="<?php echo $price; ?>" required>
								<button type="submit"  onclick="return confirm('確認更新?')"  class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-6 offset-3">確認更新</button> 
							</form>					
						<?php	
						}
						?>
						</div>
						
					</div>
				</div>
			</div>
			<!-- 個別房間設定_現在改為110電燈插座_電錶設定 END-->

			<!-- 個別房間設定_現在改為220電燈插座_電錶設定 -->
			<div class="col-lg-6">
				<div class="card shadow mb-4">
					<div class="card-header py-3">
						<h6 class="m-0 font-weight-bold text-center">
							<?php echo $lang->line('220V_meter').'<br>'.
										$lang->line('rate_air_conditioner')
							?>
						</h6>
					</div>
					<div class="card-body ">					
							
						<div class="form-group">
							<form id="myForm1" action="" method="get">
								<label class="label-center col btn-marbot20">輸入房號</label>  
								<input type="search" name="room_numbers_kw" value="<?php echo $room_num ?>" class=" form-control  col-8 offset-2" placeholder="Ex. T201" required>
								<button class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-6 offset-3">查詢</button>
							</form>
						</div>
						
						<div class="form-group  btn-martop30">
						<?php
						if($room_id != '') {
						?>
							<form id="myForm3" action="model/rate_upd.php" method="post">
								<input type='hidden' name='room_id'  value='<?php echo $room_id ?>'>
								<input type='hidden' name='room_num' value='<?php echo $room_num ?>'>
								<label class="label-center col btn-marbot20">用電度數</label>
								<input step="0.1" min="0" max="25.5" type="number" required="required" class="form-control col-8 offset-2" name="price_elec_degree" value="<?php echo $price; ?>" required>
								<button type="submit"  onclick="return confirm('確認更新?')"  class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-6 offset-3">確認更新</button> 
							</form>					
						<?php	
						}
						?>
						</div>
						
					</div>
				</div>
			</div>
			<!-- 個別房間設定_現在改為220電燈插座_電錶設定 END-->

			<!-- 全部房間設定_目前隱藏 -->
			<div class="col-lg-6 d-none">
				<div class="card shadow mb-4">

					<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-center">全部房間設定</h6>
					</div>
					<div class="card-body">
					<!-- 房間設定管理員權限管理 -->
						<form id="myForm2" action="model/rate_all_upd.php" method="post">
						<!--用電度數 全部設定-->
							<div class="form-group">
								<label for="exampleInputPassword1"  class="label-center col btn-marbot20">用電度數</label>
								<input step="0.1" min="0" max="25.5" type="number" required="required" class="form-control col-8 offset-2" name="price_elec_degree"  value='' placeholder="Ex:4.5" required>
								<button type="submit" onclick="return confirm('確認更新?')"  class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold btn-primary2 col-6 offset-3">確認更新</button> 
							</div>
						</form>
					</div>

				</div>
			</div>
			<!-- 全部房間設定 END-->

		</div>
	</div>

<!--NEW 費率設定 END-->

</section>

		
<?php include('footer_layout.php'); ?>
<script>
        const form_id = ['form:eq(0)','form:eq(1)','form:eq(2)','form:eq(3)'];
		// console.log(form_id);
		// $(form_id[0]).on('submit',function(e){
		// 	// e.preventDefault();
		// 	console.log(this);
		// })
</script>