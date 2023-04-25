<?php 

	include('header_layout.php');
	include('nav.php');          
	include('chk_log_in.php');
    
	$sql = "SELECT price_max, negative_limit FROM `system_info` WHERE id = '1'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$tmp = $rs->fetch();
	$price_max = $tmp['price_max'];
	$negative_limit = $tmp['negative_limit'];
?>

<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='powersetting.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>
	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center"><?php echo $lang->line("index.charge_setting"); ?></h1>
		<!--<h1 class="jumbotron-heading text-center">付費&負值上限</h1>-->
	</div>
 <!-- OLD建立新學生 修改學生
		<div class="container" style="text-align: center;">
          <p>
            <a href="newMember.php" class="btn btn-success my-2"><?php echo $lang->line("index.create_member");?></a>	 
           <a href="newPublicMember.php" class="btn btn-success my-2"><?php echo $lang->line("index.new_public_idcard"); ?></a> 
            <a href="MemberStudent.php?betton_color=primary" class="btn btn-info my-2"><?php echo $lang->line("index.edit_member");?></a>
          </p> 
    </div>
 -->
	<div class="row container-fluid mar-bot50 mar-center2">
	<?php if($_GET['success'] == 1){ ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong><?php echo $lang->line("index.success_charge_setting"); ?>!!</strong>
		</div>
	<?php } elseif($_GET['error'] == 1){ ?> 
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
		  <strong>Error</strong>資料輸入錯誤或不存在
		</div>	
	<?php } ?>
	</div>    


<!--收費設定-->
<div class="inner">   
	<div class="row">
		<!-- 付費上限 -->
		<div class="col-sm-10 offset-sm-1">
			<div class="card shadow mb-4">
				<!-- <div class="card-header ">-->
					<!-- <h6 class="m-0 font-weight-bold text-center">付費上限</h6> -->
                <!-- </div>-->
                <div class="card-body ">
						<div class="form-group">
							<form id="myForm" action="model/price_max_upd.php" method="post"  >
							<label for="exampleInputEmail1" class="label-center col btn-marbot20">付費上限設定</label>  
							<input  min="1" max="1000" type="number" class="form-control col-8 offset-2" required='required' name="price_max" value="<?php echo $price_max ?>">
							<button type="submit"  onclick="return confirm('確認更新?')" class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-6 offset-3">確認更新</button> 
							</form>
						</div>
                </div>
			</div>
		</div>

		<!-- 負值上限
		<div class="col-lg-6">
			<div class="card shadow mb-4">
				<div class="card-header ">
					<h6 class="m-0 font-weight-bold text-center">負值上限</h6>
                </div>
                <div class="card-body ">
						<div class="form-group">
							<form id="myForm" action="model/negative_limit_upd.php" method="post"  >
							<label for="exampleInputEmail1" class="label-center col btn-marbot20"></label>  
							<input  min="-2500" max="0" type="number" class="form-control col-8 offset-2" required='required' name="negative_limit" value="<?php echo $negative_limit ?>">
							<button type="submit"  onclick="return confirm('確認更新?')" class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-6 offset-3">確認更新</button> 
							</form>
						</div>
                </div>
			</div>
		</div>
		 -->

	</div>
</div>
</section>

		

<?php include('footer_layout.php'); ?>