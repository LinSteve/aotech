<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  
  <title>【AOTECH】 智慧後台</title>
  <title><?php //echo $lang->line("web-title") ?></title>
  <!-- Custom fonts -->
  <link rel="Shortcut icon" type="image/x-icon" href="../img/icon.png" />
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">

    <!-- bootstrap-select -->
    <link  rel="stylesheet" type="text/css" href="css/bootstrap-select/custom-theme/css/bootstrap-select.css" >
    
    <!-- fontawesome free online CDN-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.9/css/all.css" integrity="sha384-5SOiIsAziJl6AWe0HWRKTXlfcSHKmYV4RBF18PPJ173Kzn7jzMyFuTtk8JA7QQG1" crossorigin="anonymous"></link>




</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">
  
  
<?php 
	include_once("../config/db.php");
	require_once("../libraries/Language.php");

	$lang = new Language();
	$lang->load("index");
?>