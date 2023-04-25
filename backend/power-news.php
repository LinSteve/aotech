<?php
    include('includes/header.php');
    include('includes/nav.php');
	
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

<div class="container-fluid">
        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">通知設定</h1>
        <p class="text-lg text-center font-weight-bold NG-color">
			
		</p>

		<!--按下更新後的提示窗:等後端串好再解除-->
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
		
	
		<!--文字編輯器
		<div class="inner">   
			<div class="row">

				<div class="col-lg-10 offset-1">
					<form action="model/news_upd.php" method="post">
					<div class="card shadow mb-4">
						<textarea name="editor1"><?php echo $contact ?></textarea>
					</div>

					<button type="submit"  class="btn  btn-h-auto text-white btnfont-30  btn-martop30 font-weight-bold  btn-primary2 col-6 offset-3"
					onclick="return confirm('確認更新?')">確認更新
					</button> 
					</form>
				</div>
			</div>
		</div>
		<br><br>-->
		
		<div class='alert alert-orange col-lg-9 offset-lg-1 mar-bot50 text-center'>
			<span class='text-orange'>
				<i class="fas fa-exclamation-circle"></i>
				系統將於固定時間發送"最新負值餘額名單"，至已設定通知人電子信箱。
			</span> 
		</div>

		
		<div class="row">
					<div class="col-lg-6">
						<!-- 新增通知人設定 -->
						<div class="card shadow mb-4">
							<div class="card-header py-3">
								<h6 class="m-0 font-weight-bold text-center">通知人設定</h6>
							</div>
							
							<div class="card-body">					
									<form method="get" action="model/contact_list_add.php">
											<label for='exampleFormControlInput1'  class='label-center col btn-marbot20'>通知人</label>
											<div class='col-8 offset-2 input-group-lg'> 
												<input type='text' class='form-control  col'  name='recipient' placeholder='' value='<?php echo $recipient ?>'>  
											</div>

											<label for='exampleFormControlInput1' class='label-center col btn-marbot20 btn-martop30' >電子信箱</label>
											<div class='col-8 offset-2 input-group-lg'>
												<input   type='email' class='form-control' required='required'  maxlength='30'  name='address' value='<?php echo $address ?>'>  
											</div>

										<br>
										<button type='submit' class='btn  btn-h-auto text-white btnfont-30 font-weight-bold  btn-primary2 col-6 offset-3'>新增</button>
									</form>                 
							</div>

						</div>
						<!-- 寄送時間設定 
						<div class="card shadow mb-4">
							<div class="card-header py-3">
								<h6 class="m-0 font-weight-bold text-center">寄送時間設定</h6>
							</div>
							<p class='text-orange text-center'>寄送時間顯示資料庫設定的值，方便告知當下設定的時間</p>
							<div class="card-body">					
									<form method="get" action="model/contact_list_add.php">
											<label for='exampleFormControlInput1'  class='label-center col btn-marbot20' >寄送時間</label>
											<div class='col-8 offset-2 input-group-lg'> 
												<input type='time' class='form-control  col'  name='recipient' placeholder='' value='<?php //echo $recipient ?>'>  
											</div>

										<br>
										<button type='submit' onclick="return confirm('確認更新?')" class='btn  btn-h-auto text-white btnfont-30 font-weight-bold  btn-primary2 col-6 offset-3'>確認更新</button>
									</form>                 
							</div>

						</div>
						-->

					</div>


					<!-- 已設定通知人 -->
					<div class='col-lg-6'>
						<div class="card shadow mb-4">
						<div class="card-header py-3">
								<h6 class="m-0 font-weight-bold text-center">已設定通知人</h6>
						</div>

						<div class="card-body">					
							<div class="table-responsive">
											<table class="table  text-center">
											<thead class="thead-green">
												<tr class="text-center">
												<th scope="col">#</th> 
												<th scope="col">通知人</th>
												<th scope="col">電子信箱</th>
												<th scope="col"><?php //echo $lang->line("index.operating"); ?></th>
												</tr>
											</thead>

											<!-- 前台有串後端資料的code-->
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
									
									<!-- 跳頁 上下頁-->
									<div class="row ">
										<div class="container-fluid ">
											<div class=" text-center" id="dataTable_paginate">
											<!-- 前台有串後端資料的code-->		
											<?php  
												if($rownum > $pagesize) {
												echo $pageurl;
												echo " ".$lang->line("index.current_page")." $page | ".$lang->line("index.a_few_pages")." $pagenum ".$lang->line("index.page")."";
											}
											?> 			  
											</div>
										</div>
									</div>
									<!-- 跳頁 上下頁 end-->

							</div>
						</div>
					</div>
					<!--已設定通知人 END-->
		</div>
				
</div>





<script>
	CKEDITOR.replace( 'editor1', {
        skin: 'blue'
} );
</script>
<script src="//cdn.ckeditor.com/4.14.0/full/ckeditor.js"></script>
<!--陽春版本 TEST後 後台仍然抓HTML到後台
<script src="//cdn.ckeditor.com/4.14.0/basic/ckeditor.js"></script>
-->
<script>CKEDITOR.replace("editor1");</script>

<!--文字編輯器  END-->


<?php
    include('includes/scripts.php');
    include('includes/footer.php');
?>