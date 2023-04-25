<?php  
	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');
 
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
		WHERE 1 ".$sql_kw." AND r.Title='研習室' ORDER BY `id`";	 
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

<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='studysearch.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">系統使用現況</h1>
    </div>
	
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
	<!-- SEARCH -->
	<div class="inner">
		<div class="row">
				<!--div class="alert alert-orange col-12">
					<p>【製作說明】</p>
					<p>1.目的：呈現研習室的使用現況</p>
					<p>2.查詢功能失效，協助修復</p>
					<p>3.協助檢查前端串資料庫後端PHP寫法有無問題，有請協助修正</p>
					<p>4.收費：計費時才顯示金額，其餘模式一律帶0</p>
					<p>5.Table:room_study_situation、member、room</p>
					<p>6.此處只會呈現研習室專用卡資料，identity=4</p>
					<p>7.注意：要考量到1張研習室專用卡，可同時刷N間的機制</p>
					<p>8.研習室電力現況(power_status)：只在"計費"時出現，顯示如下示意</p>
					<p><i class="fas fa-bolt px-1"></i>未使用:power_status=0；</p>
					<p class="text_in_use"><i class="fas fa-bolt px-1"></i>使用中:power_status=1；</p>
				</div>-->
				<form id='mform1' action="study-nowsystem.php" method="get" class='col-12'>
					<div class='col-12'>
						<section class='panel panel-noshadow'>
	             			<div class='panel-body'>
							 
							<div class='form-group row'>
								<label class='col-sm-2 col-form-label label-right' >編號</label>
								<div class='col-sm-9'> 
									<input type='text' class='form-control  col'  maxlength='10'  name='username' placeholder='全部學號/教職員工/卡號' value='<?php echo $username ?>'>  
								</div>
							</div>

							 <div class='form-group row'>
							 <label class='col-sm-2 col-form-label label-right'><?php echo $lang->line("index.room_number")?></label>
							 <div class='col-sm-9'> 
								 <input  type='text' class='form-control' name='room_strings' placeholder='全部' value='<?php echo $room_num ?>'>
							 </div>
						 	</div>

							<div class='form-group row'>
								<label class='col-sm-2 col-form-label label-right' >姓名</label>
								<div class='col-sm-9'>
									<input   type='text' class='form-control'   name='cname' placeholder='全部' value='<?php echo $cname ?>'>  
								</div>
							</div>

							<br><br>
							<input type='hidden' name='search' value='1'>
							<button type='submit' class='btn  btn-loginfont btn-primary2  col-4 offset-4'><?php echo $lang->line("index.confirm_query") ?></button>
	             			</div>
	             		</section>
	             	</div>
				</form>

		</div>
	</div>
	<!-- SEARCH END-->

<!--表格 -->
<div class='inner inner2' style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
		<h1 class="jumbotron-heading text-center h1-mar"><?php echo ($search != '') ? $lang->line("serach_results") : "" ?></h1>
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
												$show_on  = "b-label ";
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
												<b class="<?php echo $display ?>"><?php echo $tmp['cname']?>/<?php echo $tmp['username']?>/</b>
												<b class="<?php echo $display ?>"><?php echo $tmp['id_card']?></b><br>
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
<!--表格 END-->



</section>

<?php include('footer_layout.php'); ?>