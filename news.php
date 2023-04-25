<?php 
	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

	$pagesize = 10;
	
	$sql = "SELECT * FROM `system_info` WHERE `id` = 1";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetch();

	$contact = $data['contact'];
	
	if(isset($_GET['page'])) {
		$page = $_GET['page'];  
	} else {
		$page = 1;                                 
	}
	
	$sql = "SELECT * FROM `contact_list` WHERE `enable` = 1 ";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$tmp = $rs->fetch();
	$rownum = $tmp['count'];
	
	$pageurl  = '';
	$pagenum  = (int) ceil($rownum / $pagesize);  
	$prepage  = $page - 1;                        
	$nextpage = $page + 1;
	
	if($page == 1) {                         
		$pageurl .= " ".$lang->line("index.home")." | ".$lang->line("index.previous_page")." | ";
	} else {
		$pageurl .= "<a href='?page=1'>".$lang->line("index.home")."</a> | <a href='?page={$prepage}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}'>".$lang->line("index.next_page")."</a> | <a href='?page={$pagenum}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT * FROM `contact_list` WHERE `enable` = 1  ORDER BY `id` DESC Limit ". ($page-1) * $pagesize .",". $pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
?>
<!--移植已設定通知人PHP END-->




<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='powersetting.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>
	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">公告事項</h1>
	</div>
	
	<div class="row container-fluid mar-bot50">
		<?php if($_GET['success'] == 1){ ?>
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
			<strong>更新成功!!</strong>
			</div>
		<?php } elseif($_GET['error'] == 1){ ?> 
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
			<strong>更新失敗!!</strong>
			</div>	
		<?php } elseif($_GET['success'] == 2){ ?> 
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
			<strong>新增完成!!</strong>
			</div>	
		<?php } elseif($_GET['error'] == 2){ ?> 
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
			<strong>新增失敗!!</strong>
			</div>	
		<?php } ?>
	</div>  
	
	<!--文字編輯器-->
	<div class="inner">   
		<div class="row">

			<div class="col-lg-10 offset-1">
				<form action="model/news_upd.php" method="post">
					<div class="card shadow mb-4">
						<textarea name="editor1"><?php echo $contact ?></textarea>
					</div>

					<button type="submit"  class="btn  btn-h-auto text-white btnfont-30  btn-martop30 mar-bot50 font-weight-bold  btn-primary2 col-6 offset-3"
					onclick="return confirm('確認更新?')">確認更新
					</button> 
				</form>
			</div>
		</div>
	</div>

	<!--email 通知設定
	<br><br>
	<div class="inner">
			<div class="row">
						<div class='col-12'>
							<div class='panel panel-noshadow'>					
								<form method="get" action="model/contact_list_add.php">

									<div class='panel-body'>
									<h1 class="jumbotron-heading text-center">通知設定</h1>
									<div class='row h4 alert alert-orange col-10 mar-bot50 span-mar'>
											<span class='text-orange text-center'>
												<i class="fas fa-exclamation-circle" style='width:22px; height:22px;'></i>
												系統將於固定時間發送"最新負值餘額名單"，至已設定通知人電子信箱。
											</span> 
									</div>



										<div class='form-group row'>
										<label for='exampleFormControlInput1'  class='col-sm-2 col-form-label label-right' >通知人</label>
										<div class='col-sm-9'> 
										<input type='text' class='form-control  col'  name='recipient' placeholder='' value='<?php echo $recipient ?>'>  
										</div>
										</div>

										<div class='form-group row'>
										<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right' >電子信箱</label>
										<div class='col-sm-9'>
										<input   type='email' class='form-control' required='required'  maxlength='30'  name='address' value='<?php echo $address ?>'>  
										</div>
										</div>

										<br><br>
										<button type='submit' class='btn  btn-loginfont btn-primary2  col-4 offset-4'>新增</button>
									</div>
								</form>
							</div>
-->

							<!-- 通知人一覽表>

							<h1 class="jumbotron-heading text-center h1-mar">已設定通知人</h1>

							<div class="table-responsive">
									<table class="table  text-center">
									<thead class="thead-green">
										<tr class="text-center">
										<th scope="col">#</th> 
										<th scope="col">通知人</th>
										<th scope="col">電子信箱</th>
										<th scope="col"><?php echo $lang->line("index.operating"); ?></th>
										</tr>
									</thead>
									<tbody>
									<?php 
											$j = 0;
									
											foreach($data as $row) {
												
												$row_count    = ($prepage * $pagesize) + ++$j;
												
												$contact_id   = $row['id'];
												$contact_name = $row['recipient'];
												$contact_addr = $row['address'];
										?>
												<tr>
													<td scope='row'><?php echo $row_count ?></td>
													<td scope='row'><?php echo $contact_name ?></td>
													<td scope='row'><?php echo $contact_addr ?></td>
													<td scope='row'>
														<a onclick="return confirm('確認提示\n該筆資料將移除\n您確定要移除嗎?');"  href='model\contect_list_del.php?id=<?php echo $contact_id ?>' class='btn '  title='刪除'><i class='fas fa-trash-alt text-orange'></i></a>
													</td>
												</tr>
										<?php
											}
										?>						  


									</tbody>
									</table>
							</div>
							-->
							<!-- 跳頁 上下頁
							<div class="row ">
								<div class="container-fluid ">
									<div class=" text-center" id="dataTable_paginate">
									<?php  
										if($rownum > $pagesize) {
										echo $pageurl;
										echo " ".$lang->line("index.current_page")." $page | ".$lang->line("index.a_few_pages")." $pagenum ".$lang->line("index.page")."";
									}
									?> 							  
									</div>
								</div>
							</div>-->
							<!-- 跳頁 上下頁 end-->
				<!--		<br><br>

						</div>
			</div>
	 		通知人一覽表 END-->
	<!--</div>

	 新增email通知人 END-->


</section>

<script>
	CKEDITOR.replace( 'editor1', {
        skin: 'blue'
	} );
	$('#footer').css({'position' : 'static'});
</script>
<script src="//cdn.ckeditor.com/4.14.0/full/ckeditor.js"></script>
<script>CKEDITOR.replace("editor1");</script>

<?php //include('footer_layout.php'); ?>
<!-- FooterTEST -->
<footer id="footer" class="sticky-footer footer-bg">
<div class="copyright">
<?php print " &copy; AOTECH合創數位科技2020" ?>
<?php if(false && !isset($_SESSION['admin_user']['sn']) && !isset($_SESSION['user']['sn'])) { ?>
	<a href="admin_login.php"><?php Echo $lang->line("index.admin_login"); ?></a>.
<?php } ?>
</div>
</footer>		
<!-- </section> -->
<script src="assets/js/jquery.scrolly.min.js"></script>
<script src="assets/js/skel.min.js"></script>
<script src="assets/js/util.js"></script>
<script src="assets/js/main.js"></script>
<script>
        $(function(){
            function footerPosition(){
                var contentHeight = document.body.scrollHeight;//網頁正文全文高度
                var winHeight = window.innerHeight;//可視窗口高度，不包括瀏覽器頂部工具欄
                if(!(contentHeight < winHeight)){
                    $('#footer').css({'position' : 'fixed'});
                } else {
                    $('#footer').css({'position' : 'static'});
                }
            }
            footerPosition();
            $(window).resize(footerPosition);
        });
</script>
</body>
</html>
