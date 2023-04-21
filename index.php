<?php 

include('header_layout.php');
include('nav.php');

$message   = '';
$error_arr = array('1' => '帳號密碼錯誤，請再確認',
				   '2' => '帳號密碼錯誤，請再確認');

if(isset($_GET['error'])) {
	$message = $error_arr[$_GET['error']];
}

$rs  = $PDOLink->query("SELECT contact FROM system_info WHERE id = '1' ");
$row = $rs->fetch();
$SchoolContact = $row['contact'];
$rs  = $PDOLink->query("SELECT * FROM recaptcha_key WHERE id = '1' ");
$row = $rs->fetch();
$SITE_KEY = $row['site_key'];
$SECRET_KEY = $row['secret_key'];
define("SITE_KEY", $SITE_KEY);
define("SECRET_KEY", $SECRET_KEY);
?>

	<section id='banner' class='col-lg-12 '>
		<!--<div  class="content">-->
		<div>
			<h1 class="title">
				智慧校園管理系統
			</h1>
		</div>
		<!--管理員登入介面-->
		<div id='identity' class='col-lg-12 col-md-9' >
			<form id='adminlogin' action='model/chk_adminlogin.php' method='post' class='col-lg-12'>
				<!--<div class='login form title mb-5'></div>-->
				<h1 class='h4 mb-4'>管理員登入</h1>
				<div class='login form'>
					<div class='div_column'>
						<span class='user col-lg-12'></span>
					</div>
					<div class='div_column' >帳號&nbsp;</div>
					<div class='div_column' style='width:300px;'>
						<input class='form-control' type='text' name='id' placeholder='帳號' id='example-text-input'>
					</div>
				</div>
				<div class='login form'>
					<div class='div_column'>
						<span class='lock col-lg-12'></span>
					</div>
					<div class='div_column'>密碼&nbsp;</div>
					<div class='div_column' style='width:300px;'>
						<input class='form-control' type='password' name='pwd' placeholder='密碼' id='example-search-input'>
					</div>
				</div>
				<div class='login form notice'><?php echo $message; ?></div>
				<!-- Google reCAPTCHA widget -->
				<div class="form-row">
					<div class="g-recaptcha"
						data-sitekey="<?php echo SITE_KEY; ?>"
						data-badge="inline" data-size="invisible"
						data-callback="setResponse"></div>
				<!--	<input type="hidden" id="captcha-response" name="captcha-response" />-->
				</div>
				<div class='login form  col-lg-12'>
					<button id='btn_login' class='btn btn-loginfont btn-primary2 btn-user col-lg-12'>登入</button>
				</div>
					<input type="hidden" id="captcha-response2" name="captcha-response2" />
			</form>
		</div>
		<!--學生登入介面-->
		<div id='identity2' class='col-lg-12 col-md-9' >
			<form id='adminlogin2' action='model/chk_userlogin.php' method='post' class='col-lg-12'>
				<!--<div class='login form title mb-5'></div>-->
				<h1 class='h4 mb-4'>學生登入</h1>
				
				<div class='login form'>
					<div class='div_column'>
						<span class='user col-lg-12'></span>
					</div>
					<div class='div_column' >帳號&nbsp;</div>
					<div class='div_column' style='width:300px;'>
						<input class='form-control' type='text' name='id' placeholder='帳號' id='example-text-input'>
					</div>
				</div>
				<div class='login form'>
					<div class='div_column'>
						<span class='lock col-lg-12'></span>
					</div>
					<div class='div_column'>密碼&nbsp;</div>
					<div class='div_column' style='width:300px;'>
						<input class='form-control' type='password' name='pwd' placeholder='密碼' id='example-search-input'>
					</div>
				</div>
				<div class='login form notice'><?php echo $message; ?></div>
				<!-- Google reCAPTCHA widget -->
				<div class="form-row">
					<div class="g-recaptcha"
						data-sitekey="<?php echo SITE_KEY; ?>"
						data-badge="inline" data-size="invisible"
						data-callback="setResponse"></div>
					<input type="hidden" id="captcha-response" name="captcha-response" />
				</div>
				<div class='login form  col-lg-12'>
					<button class='btn btn-loginfont btn-primary2 btn-user col-lg-12'>登入</button>
				</div>
		
			</form>
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
	<!--系統公告
	<section>
		<div class='contain-fulid wrapper style1 special' >  
			<div id='two' class=' '>
				<h2 class='text-center'><?php echo $lang->line("index.system_title") ?></h2>
				<figure>
					<blockquote style='text-shadow: 0px 1px 0px #000;'><?php echo strip_tags($SchoolContact) ?></blockquote>
				</figure>
			</div>
		</div>
	</section>
	-->
	<!-- FooterTEST
	<footer id="footer" class="sticky-footer footer-bg">
	<div class="copyright">
	<?php print " &copy; AOTECH合創數位科技2020"//.$lang->line("index.browser_suggested_size").": 1280 * 900為佳<br> "; ?>
	<?php if(false && !isset($_SESSION['admin_user']['sn']) && !isset($_SESSION['user']['sn'])) { ?>
		<a href="admin_login.php"><?php Echo $lang->line("index.admin_login"); ?></a>.
	<?php } ?>
	</div>
	</footer>
	 -->	
	<?php include('footer_layout.php'); ?>
	<!--
	<script src="assets/js/jquery.scrolly.min.js"></script>
	<script src="assets/js/skel.min.js"></script>
	<script src="assets/js/util.js"></script>
	<script src="assets/js/main.js"></script>
	-->
	
<!--置換背景效果-->
<script>
	
	var counter = 0;
	var chk_msg = "<?php echo $message; ?>";
  
	if(chk_msg != '') {
		alert(chk_msg);
	}
  
	$('#btn_login').click(function() {
		document.getElementById('captcha-response2').value = $('#captcha-response').val();
		$('#adminlogin').submit();
	});
		
	$(document).ready(function() {
		
		// 底圖 -- 20200227
		$('.button.alt').click(function() {
			
			$('#identity2').hide();
			
			if($('#identity').css('display') == 'block') {
				$('#banner').css("background-image", "url(img/bk2.jpg)");
				$('h1.title').css("display", "none");
			} else {
				$('#banner').css("background-image", "url(img/bk.jpg)");
			}
		});
		
		$('.button.alt2').click(function() {
			
			$('#identity').hide();
			
			if($('#identity2').css('display') == 'block') {
				$('#banner').css("background-image", "url(img/bk2.jpg)");
				$('h1.title').css("display", "none");
			} else {
				$('#banner').css("background-image", "url(img/bk.jpg)");
			}
		});
		
		$('.for_mobile').click(function() {
			$('#identity').hide();
			$('#identity2').hide();
		});
	});
</script>