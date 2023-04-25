<?php
    include('includes/header.php');
	include('includes/scripts.php');
define("SITE_KEY", "6LdfXCklAAAAACuqgdBBsz9lpfkgCZqd-335vyz7");
define("SECRET_KEY", "6LdfXCklAAAAAPdlEtagQRtvfbX560Gneg-w2GFo");
?>
<body class="bg-gradient-primary">

<div class="container">

  <!-- Outer Row -->
  <div class="row justify-content-center">

    <div class="col-xl-5 col-lg-6 col-md-9">

      <div class="card o-hidden border-0 shadow-lg my-4">
        <div class="card-body p-0">
          <!-- Nested Row within Card Body -->
          <div class="row">
            <div class="col-lg-12">
              <div class="p-5">
                <div class="text-center">
                  <p class=""><img width="200" src="img/logo.png" alt="test"></p>
                  <h1 class="h4 text-gray-900 mb-4">【國立臺北科技大學】 <br>智慧管理後台</h1>
                </div>
                <form class="user" action='chk_adminlogin.php' method='post'>
                  <div class="form-group form-inline input-group-lg">
                    <label class="col-sm-2 col-form-label label-center"><i class="fas fa-user mr-4 text-green"></i></label>
                    <input type="text" class="form-control col-sm-10" name="id" aria-describedby="emailHelp" placeholder="帳號">
                  </div>
                  <div class="form-group form-inline input-group-lg">
                    <label class="col-sm-2 col-form-label label-center"><i class="fas fa-unlock-alt mr-4 text-green"></i></label>
                    <input type="password" class="form-control col-sm-10" name="pwd" placeholder="密碼">
                  </div>
				<!-- Google reCAPTCHA widget -->
				<div class="form-row">
					<div class="g-recaptcha"
						data-sitekey="<?php echo SITE_KEY; ?>"
						data-badge="inline" data-size="invisible"
						data-callback="setResponse"></div>
					<input type="hidden" id="captcha-response" name="captcha-response" />
				</div>
				<!--
                  <a href="index.php" class="mt-4 btn btn btnfont-30 text-white btn-primary2 btn-user col">
                    登入
                  </a>
                  <button type="button" class="btn btnfont-30 text-white btn-primary2 col">
                    <span>登入</span>
                  </button>
-->
					<input type='submit' class='mt-4 btn btn btnfont-30 text-white btn-primary2 btn-user col' value='登入'>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
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

</div>
 
</body>