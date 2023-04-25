<?php 

	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

	$sn = $_SESSION['admin_user']['sn'];
	$id = $_SESSION['admin_user']['id'];

	$list_q = "SELECT * FROM `member` WHERE `id` = ".$id;
	$list_r = $PDOLink->prepare($list_q); 
	$list_r->execute();
  
	$row    = $list_r->fetch();
	$sn     = $row['sn'];
	$id     = $row['id'];
	$pwd    = $row['pwd'];
	$cname  = $row['cname'];
	$mobile = $row['mobile'];
	$ext    = $row['ext'];
	$email  = $row['email'];
?>
<section id="main" class="wrapper">
	<!-- <div class='rwd-box'></div> -->
<div class="col-12 btn-back"><a href="new-member2.php" ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>
	

	
	<div class="col-12 row container-fluid mar-bot50 mar-center2">
	<?php if($_GET[success]){ ?>
		<div style="margin: 0 auto; text-align: center;  width: 600px;" class="alert alert-success" role="alert">
		  <strong>系統已成功設定!!</strong> 
		</div>
	<?php } elseif ($_GET[error] == 1) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong>您的"舊密碼"沒填或輸入錯誤!!</strong>
		</div>
	<?php } elseif ($_GET[error] == 2) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong>您的"新密碼"沒填!!</strong>
		</div>	
	<?php } elseif ($_GET[error] == 3) { ?>
		<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
		  <strong>您的"確認密碼"沒填!!</strong>
		</div>			
	<?php } ?>
	</div>


	
	<div class="inner inner2">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<h1 class="jumbotron-heading text-center">密碼變更</h1>
					
					<form id='mform' action="model/admin_upd.php" method="post">
						<div class="form-group row">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right">您的舊密碼</label>
    						<div class="col-sm-9"> 
								<input  type="password" required='required' class="form-control  col" name="o_pwd" id="o_pwd" placeholder="" >
    						</div>
						</div>

  						<div class="form-group row">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right">您的新密碼</label>
    						<div class="col-sm-9">
								  <input type="password" required='required' class="form-control" name="new_pwd" id="new_pwd" title="請輸入4~8碼"  placeholder="請輸入4~8碼" inputmode="numeric"  minlength="4" maxlength="8">  
    						</div>
						</div>

						<div class="form-group row">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right">確認新密碼</label>
    						<div class="col-sm-9">
								  <input type="password" required='required' class="form-control" name="new_pwd_check" id="new_pwd_check"  title="請輸入4~8碼"  placeholder="請輸入4~8碼" inputmode="numeric"  minlength="4" maxlength="8">  
    						</div>
						</div><br><br>
					    <input type="hidden" name="sn" value="<?php echo $id; ?>">
					    <button type="submit" onclick="return confirm('確認修改?')" class="btn  btn-loginfont btn-primary2  col-sm-4 offset-sm-4">確認修改</button>
					</form>

				</div>
			</div>
		</div>
	</div>

</section>

<style>
.table1>tbody>tr>td{
	text-align: right;
    vertical-align: middle;
}
</style>

<script>
$(document).ready(function(){
	
	$('#mform').submit(function(event) {
		if($('#new_pwd').val() != $('#new_pwd_check').val()) {
			alert("新密碼與確認新密碼不一致");
			return false;
		};
	});
});
</script>
<?php include('footer_layout.php'); ?>
<script>	
		
// alert($('#main').height());
		
		// -- 20200311
		if($('#main').height() > 446) {
			
			$('#footer').css({'position' : 'fixed'});
			$('#footer').css({'height' : 'auto'});
			$('#footer').css({'padding' : '10px 0'});


		}
</script>