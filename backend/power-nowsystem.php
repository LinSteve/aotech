<?php 

	include('includes/header.php');
	include('includes/nav.php');	
	include('includes/scripts.php');
	include('chk_log_in.php');

 	$pagesize = 10;
	
	$sql_kw   = "";
	$now_time = date('Y-m-d H:i:s');
	
	$username = $_GET['username'];
 	$room_num = $_GET['room_strings'];
	$cname    = $_GET['cname'];
	$dong     = $_GET['dong'];
	$floor    = $_GET['floor'];
	$search   = $_GET['search'];
	
	if($username) { $sql_kw .= " AND `name` IN (SELECT room_strings FROM member 
								 WHERE `username` like '%".trim($username)."%' OR `id_card` like '%".trim($username)."%') "; }
	if($cname)    { $sql_kw .= " AND `name` IN (SELECT room_strings FROM member WHERE `cname` like '%".trim($cname)."%') "; }
	if($room_num) { $sql_kw .= " AND concat(r.name, m.berth_number) LIKE '%".trim($room_num)."%' "; }
	if($dong)     { $sql_kw .= " AND `dong` = '{$dong}' "; }
	if($floor)    { $sql_kw .= " AND `floor` = '{$floor}' "; }
	
	$sql = "SELECT DISTINCT r.* FROM `room` r
			LEFT JOIN member m ON r.`name` = m.room_strings
			WHERE 1 ".$sql_kw." ORDER BY `name`";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	$sql = "SELECT dong, floor FROM `room` GROUP BY dong, floor";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$room_arr  = $rs->fetchAll();
	$dong_arr  = array();
	$floor_arr = array();
	
	foreach($room_arr as $v) {
		$dong_arr[$v['dong']]   = $v['dong'];
		$floor_arr[$v['floor']] = $v['floor'];
	}
	
	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'mode'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$mode_arr = $rs->fetchAll();
	$mode_map = array();

	foreach($mode_arr as $v) {
		$mode_map[$v['custom_id']] = $v['custom_var'];
	}
	
	//asort($dong_arr);
	//asort($floor_arr);
	ksort($dong_arr);
    ksort($floor_arr);
//棟別
	$sql = "SELECT dong,dong_name FROM `dongname` ";
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
  	$room_arr  = $rs->fetchAll();
  
  foreach($room_arr as $v) {
    $dong_Map[$v['dong']] = $v['dong_name'];
    if(!in_array($v['dong_name'],$dong_Map))
    {
     $dong_Map[$v['dong_name']] = $v['dong'];
    }
   }

?>
<div class="row mar-center2" style="margin: 0 auto;">
	<?php if($_GET['error'] == 1){ ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
		  <strong>沒有可匯出的資料！！</strong>
		</div>
	<?php } elseif ($_GET['error'] == 2) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
		  <strong>Error Error！！</strong>
		</div>
	<?php } elseif ($_GET['success']) { ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-success col-lg-9" role="alert">
		  <strong>Success 成功設置！！</strong>
		</div>
	<?php } ?>
</div>
<!-- Begin Page Content -->
<div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">系統使用現況</h1>
		<!-- SEARCH-->
		<div class='col-12'>
				<form id='mform1' action="" method="get">

					<div class='form-group row '>
						<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'><?php echo $lang->line("index.room_bed_number")?></label>
						<div class='col-sm-8 input-group-lg'> 
							<input  type='text' class='form-control' name='room_strings' placeholder='全部' value='<?php echo $room_num ?>'>
						</div>
					</div>


					<div class='form-group row '>
						<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>編號</label>
						<div class='col-sm-8 input-group-lg'> 
							<input type='text' class='form-control  col'  maxlength='10'  name='username' placeholder='全部學號/教職員工/卡號' value='<?php echo $username ?>'>  
						</div>
					</div>


					<div class='form-group row '>
						<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>姓名</label>
						<div class='col-sm-8 input-group-lg'> 
							<input   type='text'  class='form-control' name='cname' placeholder='全部' value='<?php echo $cname ?>'>  
						</div>
					</div>
															
					<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right pd-top25'>棟別</label>
						<div class='col-sm-8 form-inline'> 
								<select  class='col form-control selectpicker custom-select-lg' size='1' name='dong'>
									<option value=''>全部</option>
									<?php
										foreach($dong_arr as $v) {
											$opt_key = $v;
											$opt_val = $v;
											$select = ($opt_key == $dong) ? 'selected' : '';
											echo "<option value='{$v}'{$select}>{$dong_Map[$v]}</option>";
											//echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
										}
									?>
								</select>
						</div>
					</div>		

					<div class='form-group row'>
						<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right pd-top25'>樓層</label>
							<div class='col-sm-8 form-inline'> 
								<select  class='col form-control selectpicker custom-select-lg' size='1' name='floor'>
									<option value=''>全部</option>
										<?php
											foreach($floor_arr as $v) {
												$opt_key = $v;
												$opt_val = $v;
												$select = ($opt_key == $floor) ? 'selected' : '';
												echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
											}
										?>
								</select>
							</div>
					</div>		

				

					<br>
					<input type='hidden' name='search' value='1'>
					<button type='submit' class='btn btnfont-30 btn-primary2 text-white col-sm-4 offset-sm-4'><?php echo $lang->line("index.confirm_query") ?></button>
				</form>
	    </div>
		<!-- SEARCH END-->
</div>
<!-- /.container-fluid -->
<!--查詢結果 -->
<div class="container-fluid">
		<div style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
				<!--<h1 class="jumbotron-heading text-center h1-mar"><?php echo ($search != '') ? $lang->line("serach_results") : "" ?></h1>-->
							<?php
							$dong_old;
							$floor_old;
							$flag  = false;
							
							foreach($data as $v) {

								$room_id  = $v['id'];
								$dong     = $v['dong'];
								$floor    = $v['floor'];
								$room_num = $v['name'];
								$rate     = $v['price_degree'];
								$amount   = $v['amount'];
								$desc     = $mode_map[$v['mode']];
								
								if($floor != $floor_old || $dong != $dong_old) {
									
									if($flag) { echo "</div>"; }
									
									$flag = true;
									$floor_old = $floor;
									$dong_old  = $dong;
							?>
								<div class="my-2">
									<h5 class="text-gray-900 font-weight-bold"><?php echo $dong_Map[$v['dong']] //echo $dong?>/<?php echo $floor ?>F</h5>
									<h5 class="text-gray-900 font-weight-bold">更新時間:<?php echo $now_time ?></h5>
								</div>
								<div class="row">
							<?php
								}
							?>
								<div class="col-lg-3 col-sm-4 card-group">
								<div class="card card-h mb-4 card-green text-green fz-18 h-auto">
									<div class="py-3 nowsystem">
										<ul>
											<li>
												房號/狀態：<?php echo $room_num ?>/<span class='' style="color:#24D354"><?php echo $desc ?><span>
											</li>
											<li>電錶/費率：<?php echo $amount ?>/<?php echo $rate ?></li>
										</ul>

										<ul>
											<li>
												<div class="b-align">
													<p class="my-0">床號/姓名/學號/卡號/餘額</p>
													<?php
														$show_on  = "";
														$show_off = "bg-gray-500";
													
														$sql = "SELECT rsc.*, m.cname, m.username, m.id_card, m.balance, m.berth_number
																FROM `room_electric_situation` rsc
																LEFT JOIN `member` m ON m.id = rsc.member_id
																WHERE m.del_mark = 0 AND rsc.room_id = ".$room_id." ORDER BY berth_number";
														$rs  = $PDOLink->prepare($sql);
														$rs->execute();
														$block_arr = $rs->fetchAll();
														
														foreach($block_arr as $tmp) {
															
															$display = $tmp['powerstaus'] == 1 ? $show_on : $show_off;
															$balance = round($tmp['balance'], 1);
													?>
														<b class="b-label <?php echo $display ?>"><?php echo $tmp['berth_number']?>/<?php echo $tmp['cname']?>/<?php echo $tmp['username']?>/</b>
														<b class="b-label <?php echo $display ?>"><?php echo $tmp['id_card']?>/<?php echo $balance?></b><br>
													<?php
														}
													?>
												</div>
											</li>
										</ul>
									</div>
								</div>
								</div>
							<?php
							}
							
							if($flag) { echo "</div>"; }
							?>
			

		</div>
</div>
<!--查詢結果-->

<!-- 教官查詢房號  

	
	<div class="inner">
		<div class="row">-->  
			<!--<a href="member2.php"><i class="fas fa-chevron-circle-left fa-3x"></i></a><br>-->
			<!-- SEARCH 
				<form id='mform1' action="power-nowsystem.php" method="get" class='col-12'>
					<div class='col-12'>
						<section class='panel panel-noshadow'>
	             			<div class='panel-body'>
							 
							<div class='form-group row'>
								<label for='exampleFormControlInput1'  class='col-sm-2 col-form-label label-right' >編號</label>
								<div class='col-sm-9'> 
									<input type='text' class='form-control  col'  maxlength='10'  name='username' placeholder='全部學號/教職員工/卡號' value='<?php echo $username ?>'>  
								</div>
							</div>

							 <div class='form-group row'>
							 <label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>房號</label>
							 <div class='col-sm-9'> 
								 <input  type='text' maxlength='5' class='form-control' name='room_strings' placeholder='全部' value='<?php echo $room_num ?>'>
							 </div>
						 	</div>

							<div class='form-group row'>
								<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right' >姓名</label>
								<div class='col-sm-9'>
									<input   type='text' class='form-control'   name='cname' placeholder='全部' value='<?php echo $cname ?>'>  
								</div>
							</div>

							<div class="form-group row select-mar2">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">棟別</label>	
							<div class="col-sm-9  form-inline">
								<select class="room_changes col form-control  selectpicker show-tick" size="1" name="dong"  >
								<option value=''>全部</option>
								<?php
									foreach($dong_arr as $v) {
										$opt_key = $v;
										$opt_val = $v;
										$select = ($opt_key == $dong) ? 'selected' : '';
										echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
									}
								?>
								</select>
							</div>
							</div>							
							 
							<div class="form-group row select-mar4">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">樓層</label>	
							<div class="col-sm-9  form-inline">
								<select class="room_changes col form-control  selectpicker show-tick" size="1" name="floor" >
								<option value=''>全部</option>
								<?php
									foreach($floor_arr as $v) {
										$opt_key = $v;
										$opt_val = $v;
										$select = ($opt_key == $floor) ? 'selected' : '';
										echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
									}
								?>
								</select>
							</div>
							</div>							
							 
  
							<br><br>
							<input type='hidden' name='serach' value='1'>
							<button type='submit' class='btn  btn-loginfont btn-primary2  col-4 offset-4'><?php echo $lang->line("index.confirm_query") ?></button>
	             			</div>
	             		</section>
	             	</div>
				</form>
			
		</div>
	</div>-->
<!-- SEARCH END-->

<!--test-->


<?php    
    include('includes/footer.php');
?>