<?php 
	
	include_once('config/db.php');

	define('ROOT_PATH', __DIR__);
	
	require_once(ROOT_PATH . "/libraries/Language.php");
	
	$lang = new Language();
	$lang->load("index");
?>
<!DOCTYPE HTML>    
<html>
	<head>  
	   <title><?php echo $web_title; ?></title>
	   <meta charset="utf-8" />
	   <meta name="viewport" content="width=device-width, initial-scale=1" />
	   <link rel="Shortcut icon" type="image/x-icon" href="img/icon.png" />


    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
	<!-- school css & RWD link -->
    <link rel="stylesheet" href="assets/css/main.css" />

	<!-- 多一個三角 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">

	<!-- Custom styles for this template-->
	<link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
	<!--CDN-->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/bootstrap-select/custom-theme/css/bootstrap-select.css">
    
    <script src="assets/bootstrap-select/custom-theme/js/jquery.min.js"></script>
    <script src="assets/bootstrap-select/custom-theme/js/bootstrap-select.js"></script>
	   <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

	   <!-- school css & RWD link -->
	   <link rel="stylesheet" href="assets/css/main.css" />

	   <!-- bootstrap online link -->
	   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
		
	   <!-- Custom styles for this template-->
		  <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">

		  <!--調整用CSS-->
		  <link href="assets/css/style.css" rel="stylesheet">

	  <!-- jQuery loading 效果  -->
	  <link href="http://yandex.st/highlightjs/8.0/styles/default.min.css" rel="stylesheet">
	  <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/randomcolor/0.4.2/randomColor.min.js"></script>
	   <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous">
	   </script>
	   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous">
	   </script>
	   <!-- fontawesome free online CDN-->
	   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.9/css/all.css" integrity="sha384-5SOiIsAziJl6AWe0HWRKTXlfcSHKmYV4RBF18PPJ173Kzn7jzMyFuTtk8JA7QQG1" crossorigin="anonymous"></link>
	</head>
<body> 
