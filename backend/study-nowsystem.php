<?php  
    include('includes/header.php');
    include('includes/nav.php');
	include('includes/scripts.php');
 
	$sql_kw   = "";
	$ex_cards = "1449350096"; // 維修卡 -- 20200908
	$now_time = date('Y-m-d H:i:s');
	
	$username = trim($_GET['username']);
 	$room_num = trim($_GET['room_strings']); 
 	$cname = trim($_GET['cname']); 
	
	$search   = $_GET['search'];
	
	if($username)
	{ 
		if($username == $ex_cards) 
		{ 
			$sql_kw .= " AND rss.power_staus = '1' AND m.id_card = '{$username}') ";
		} else {
			$sql_kw .= " AND m.username  like '%{$username}%' OR m.id_card like '%{$username}%'  ";
		}
	}
	if($cname)  $sql_kw .= " AND m.cname  like '%{$cname}%'  "; 
	if($room_num)  $sql_kw .= " AND r.`name`  LIKE '%{$room_num}%' ";  

	$sql = "
		SELECT rss.*, r.name, r.amount, r.mode, r.dong, r.floor
		FROM `room_study_situation` rss
		INNER JOIN  `room` r  ON  r.id = rss.room_id
		LEFT JOIN `member` m ON  m.id = rss.member_id
		WHERE 1 ".$sql_kw." ORDER BY `id`";	 
	// echo 	$sql ;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	$PowerStatusTypes = array('1' => '使用中', '0' => '未使用');

	$sql = "SELECT * FROM `custom_variables` WHERE custom_catgory = 'mode'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$mode_arr = $rs->fetchAll();
	$mode_map = array();

	foreach($mode_arr as $v) {
		$mode_map[$v['custom_id']] = $v['custom_var'];
	}

	$sql = "SELECT dong_name FROM dongname WHERE dong = 'B'";	 // 棟別名稱
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
  	$dong_data  = $rs->fetch(); 
	
?>



<!-- SEARCH -->

<div class="container-fluid">
		<h1 class="mb-2 font-weight-bold">系統使用現況</h1>
		<div class='col-12'>
			<form id='mform1' action="" method="get">
							 
				<div class='form-group row'>
					<label class='col-sm-2 col-form-label label-right' >卡號/編號</label>
					<div class='col-sm-8 input-group-lg'> 
						<input type='text' class='form-control  col'  maxlength='10'  name='username' placeholder='全部學號/教職員工/卡號' value='<?php echo $username ?>'>  
					</div>
				</div>

					<div class='form-group row'>
					<label class='col-sm-2 col-form-label label-right'><?php echo $lang->line("index.room_number")?></label>
					<div class='col-sm-8 input-group-lg'> 
						<input  type='text' class='form-control' name='room_strings' placeholder='全部' value='<?php echo $room_num ?>'>
					</div>
				</div>

				<div class='form-group row'>
					<label class='col-sm-2 col-form-label label-right' >姓名</label>
					<div class='col-sm-8 input-group-lg'>
						<input   type='text' class='form-control'   name='cname' placeholder='全部' value='<?php echo $cname ?>'>  
					</div>
				</div>
				<br>
				<input type='hidden' name='search' value='1'>
				<button type='submit' class='btn btnfont-30 btn-primary2 text-white col-sm-4 offset-sm-4'><?php echo $lang->line("index.confirm_query") ?></button>
			</form>
		</div>
</div>
<!-- SEARCH END-->
<div class="container-fluid">
	<div style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
					<?php
					$dong_old;
					$floor_old;
					$flag  = false;
					
					foreach($data as $v) {

						$power_status  = $v['power_status'];
						$room_id  = $v['room_id'];
						$dong     = $v['dong'];
						$floor    = $v['floor'];
						$room_num = $v['name'];
						$rate     = $v['rate'];
						$amount   = $v['amount'];
						$st_date   = $v['start_date'];
						$en_date   = $v['end_date'];
						$desc     = $mode_map[$v['mode']];
						
						if($floor != $floor_old || $dong != $dong_old) {
							
							if($flag) { echo "</div>"; }
							
							$flag = true;
							$dong_old  = $dong;
							$floor_old = $floor;
					?>
						  <div class="my-2">
							  <h5 class="text-gray-900 font-weight-bold"><?php echo $dong_data['dong_name']."/".$floor."F";?></h5>
							  <h5 class="text-gray-900 font-weight-bold">更新時間:<?php echo $now_time ?></h5>
						  </div>
						  <div class="row">
					<?php
						}
					?>
                        <div class="col-lg-3 col-sm-4 card-group">
                          <div class="card card-h mb-4 card-green text-green fz-18 h-auto">
                            <div class="py-3 nowsystem">
								<?php 
									if($power_status == 1) {
										$status_style = "text_in_use";
									} else {
										$status_style = "text_normal";
									}
									if($v['mode'] == 1 ){ // 計費
										print "<p class='text-center {$status_style}'><i class='fas fa-bolt px-1'></i>{$PowerStatusTypes[$power_status]}</p>";
									}else{
										$rate = 0;
									}
								?>
							
								<ul class="px-1">
									<li>
										房號/模式：<?php echo $room_num ?>/<span class='' style="color:#24D354"><?php echo $desc ?><span>
									</li>
									<li>電錶：<?php echo $amount ?></li>
									<li>收費：<?php echo $rate ?>/hr</li>
									<li>開始時間：<?php echo $st_date ?></li>
									<li>結束時間：<?php echo $en_date ?></li>
								</ul>

								<ul class="px-1">
									<li>
										<?php 
										if($v['mode'] == 1 )
										{
											print "<div class='b-align'>";
										}else{
											print "<div class='b-align' style='display:none'>";
										}		
										?> 
											<p class="my-0">姓名/學號/卡號</p>
											<?php
												$show_on  = "";
												$show_off = "bg-gray-500";

												$sql = "SELECT rss.*, m.cname, m.username, m.id_card, m.balance 
												FROM `room_study_situation` rss
												LEFT JOIN `member` m ON m.id = rss.member_id
												WHERE m.del_mark = 0 AND rss.room_id = {$room_id} ORDER BY berth_number";		
												$rs  = $PDOLink->prepare($sql);
												$rs->execute();
												$block_arr = $rs->fetchAll();
												
												foreach($block_arr as $tmp) {
													$display = $tmp['power_status'] == 1 ? $show_on : $show_off;
											?>
												<b class="b-label <?php echo $display ?>"><?php echo $tmp['cname']?>/<?php echo $tmp['username']?>/</b>
												<b class="b-label <?php echo $display ?>"><?php echo $tmp['id_card']?></b><br>
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
</section>

<?php include('includes/footer.php'); ?>