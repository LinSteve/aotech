<?php include('header_layout.php'); ?>
<?php include('nav.php'); ?>
<?php include('chk_log_in.php'); ?>
<?php
$rs  = $PDOLink->query("SELECT * FROM recaptcha_key WHERE id = '1' ");
$row = $rs->fetch();
$SITE_KEY = $row['site_key'];
$SECRET_KEY = $row['secret_key'];
define("SITE_KEY", $SITE_KEY);
define("SECRET_KEY", $SECRET_KEY);
?>
<section id="main" class="wrapper">
	<div class='rwd-box'></div><br>
		<div class="row">
		<?php if($_GET['success'] == 1){ ?>
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
			  <strong>您的問題我們已收到，我們會盡快為您服務！</strong>
			</div>
		<?php } elseif($_GET['error'] == 1){ ?>
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
			  <strong>查無此學號</strong>
			</div>
		<?php } elseif($_GET['error'] == 2){ ?>
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
			  <strong><?php echo $lang->line("index.error"); ?>!!</strong>
			</div>
		<?php } elseif($_GET['error'] == 3){ ?>
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
			  <strong><?php echo $lang->line("index.error"); ?>!!</strong>
			</div>
		<?php } elseif($_GET['error'] == 4){ ?>
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
			  <strong>請勿重複寄客服信</strong>
			</div>
		<?php } elseif($_GET['error'] == 5){ ?>
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
			  <strong>查無此房號</strong>
			</div>
		<?php } ?>
		</div>
	<div class="inner">
		<header class="align-center">
			<h1><br><?php echo $lang->line("index.customer_service"); ?></h1><br>
		</header>
		<div class="image fit">
			<img src="images/Call-center.jpg" alt="" />
		</div>
		<div class="col-12">
		<h3>智慧管理系統 問題反應</h3>
		<form action="content_us_add.php" enctype="multipart/form-data"  method="post">
			<div class="form-group row">
			  <label for="example-text-input" class="col-12 col-form-label"><?php echo $lang->line("index.basic_information"); ?></label>		  
			  <div class="col-12">
			  	<div class="form-group">
			  	  <input id="RoomNumberValue" required class="form-control" type="text" name="room_number" placeholder="<?php echo $lang->line("index.please_enter_room_number"); ?>" >
			  	 </div>
			  	 <div class="form-group">
			  	  <input id="UsernameValue" required class="form-control" type="text" name="username_number" placeholder="<?php echo $lang->line("index.please_enter_username"); ?>" >
			  	 </div>
			  	 <div class="form-group">
			  	  <input id="TitleValue" class="form-control" type="text" name="title" placeholder="<?php echo $lang->line("index.please_enter_name"); ?>" >
			  	 </div>
			  </div>
			   <label  for="example-text-input" class="col-12 col-form-label">處理進度回覆方式</label>	
			   <div class="col-12"> 
			  	 <div class="form-group">
			  	  <input class="form-control" required type="text" name="phone" placeholder="輸入聯繫電話">
			  	 </div>			  	 
			  	 <div class="form-group">
			  	  <input id="eMailValue"  required class="form-control" type="email" name="email" placeholder="輸入e-mail"  >
			  	 </div>
			  </div>
			  <label  for="example-text-input" class="col-12 col-form-label">儲值主機操作</label>
			  <div class="col-10">
					<div class="form-check">
					  <input class="form-check-input" type="checkbox" name="host_type[]" value="無此卡號" id="defaultCheck12">
					  <label class="form-check-label pd-content_us" for="defaultCheck12">無此卡號</label>
					</div>
			  </div>
			   <div  class="col-10">
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="host_type[]" value="確認已於超商儲值完畢，但儲值主機付款時，顯示付款失敗" id="defaultCheck5">
						<label class="form-check-label pd-content_us" for="defaultCheck5">確認已於超商儲值完畢，但儲值主機付款時，顯示付款失敗</label>
					</div>
			  </div>
			  <div class="host_other_css col-12">
			  	 <input class="form-control" type="text" name="host_other" placeholder="其他狀況說明" >
			  </div>
			   <label  for="example-text-input" class="col-12 col-form-label">房內卡機使用</label>
			   <div  class="col-10">
					<div class="form-check">
					  <input class="form-check-input" type="checkbox" name="room_type[]" value="卡片感應後綠燈未亮" id="defaultCheck6">
					  <label class="form-check-label pd-content_us" for="defaultCheck6">卡片感應後綠燈未亮</label>
					  <input class="form-check-input" type="checkbox" name="room_type[]" value="綠燈亮但電力無法開啟" id="defaultCheck13">
					  <label class="form-check-label pd-content_us" for="defaultCheck13">綠燈亮但電力無法開啟</label>
					  <input class="form-check-input" type="checkbox" name="room_type[]" value="日期及時間異常" id="defaultCheck9">
					  <label class="form-check-label pd-content_us" for="defaultCheck9">日期及時間異常</label>		
					</div>
			  </div>
			  <div class="col-10">
					<div class="form-check">
					  <input class="form-check-input" type="checkbox" name="room_type[]" value="出現 USER INVALID" id="defaultCheck7">
					  <label class="form-check-label pd-content_us" for="defaultCheck7"><?php echo $lang->line("index.USER_INVALID"); ?></label>
					  <input class="form-check-input pd-content_us" type="checkbox" name="room_type[]" value="出現CHECK BALANCE" id="defaultCheck8">
					  <label class="form-check-label pd-content_us" for="defaultCheck8"><?php echo $lang->line("index.CHECK_BALANCE"); ?></label>						
					</div>
			  </div>
			  <div  class="col-10">
					<div class="form-check">
					  <input class="form-check-input" type="checkbox" name="room_type[]" value="學生證號及系統金額有誤" id="defaultCheck11">
					  <label class="form-check-label pd-content_us" for="defaultCheck11">學生證號及系統金額有誤</label>
					</div>
			  </div>
			  <div  class="col-10">
					<div class="form-check">
					  <input class="form-check-input" type="checkbox" name="room_type[]" value="使用累計超過二小時-無扣款顯示" id="defaultCheck10">
					  <label class="form-check-label pd-content_us" for="defaultCheck10">使用累計超過二小時-無扣款顯示</label>
					</div>
			  </div>
			  <div class="col-12">
			  	 <input class="form-control orange" type="text" name="room_other" placeholder="其他狀況說明(非電力系統問題，請至學校網頁報修)" >
			  </div>
			</div>
				<div class="row">
					<div class="col-12">
							<div style="padding: 20px;" class="alert alert-success" role="alert">
							  <h4 align="center" class="alert-heading">客服反應時段</h4>
							  <p style="font-size:  18px; text-align: center;">
							  	客服時間：週一至週五09:00-18:00，不含國定假日。<br>
							  	您的問題反應，將會在客服時間內儘速回覆您，感謝您的耐心等候。<br>
								若無選項可以勾選時，請留下您的行動電話，並告知客服方便聯絡您的時間，<br>
								如造成不便，敬請見諒。
							  </p>
							</div>
					</div>	
				</div>
			<input id="loading-body2-btn" class="form-control" type='submit' value='確認送出' name='send'>
			<div class="col-12">
			<!-- Google reCAPTCHA widget -->
				<div class="form-row">
					<div class="g-recaptcha"
						data-sitekey="<?php echo SITE_KEY; ?>"
						data-badge="inline" data-size="invisible"
						data-callback="setResponse"></div>
				<input type="hidden" id="captcha-response" name="captcha-response" />
				</div>
				
			</div>
		</form>
		</div>
	</div>
</section>
	<script
		src="https://www.google.com/recaptcha/api.js?onload=onloadCallback"
		async defer></script>
	
	<script>
		var onloadCallback = function() {
			grecaptcha.execute();
		};

		function setResponse(response) { 
			document.getElementById('captcha-response').value = response; 
		}

	</script>
<div class="example col-lg-4 col-sm-6">
<script>
	// $('body').loading({
	//     stoppable: true,
	//     message: '信件正在發送中.....',
	//     theme: 'dark'
	// });
/*
$('#loading-body2-btn').click(function() {

   //取值
   room_number = $("#RoomNumberValue").val();
   username = $("#UsernameValue").val();
   title = $("#TitleValue").val();
   email = $("#eMailValue").val();
   
   //防呆判斷
   if (room_number && username && title && email) {

	      $('body').loading({
	        stoppable: true,
	        message: '信件正在發送中.....',
	        theme: 'dark'
	      });

   } else {
   	
   	alert('【提示】基本資訊和e-mail尚未填寫完');
   	return false;
   
   }

});*/
</script>
</div>
<?php include('footer_layout.php'); 
	  include('login.php');  
?>