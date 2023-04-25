<?php 

	include_once('../../config/db.php');

	include_once('hardwarelist_utility.php');

	// include('../chk_log_in.php');

	$chk_time = 1800; // 30 mins 
	$now_time = date('Y-m-d H:i:s');
	
	$sql_kw   = "";
	
	$floor    = $_POST['floor'];

	$exclu_room = array('Q6214', 'Q6215'); // 前端排除硬體錯誤
	$status_arr = getReaderData();
	$rd_error   = getReaderDeviceError($status_arr);
	$md_error   = getMeterDeviceError($status_arr);
	$pm_error   = getPowerMeterError($status_arr);
	$mr_status  = getMeterRelayStatus($status_arr);

	if($floor != '') {
		$sql_kw = " AND floor = '{$floor}'";
	}

	//$status_arr = getReaderData(); // 重複,上面已執行
	//echo 'status_arr:<BR>';
	//print_r($status_arr);
	//echo '<BR>';
	/*
	echo 'rd_error:<BR>';
	print_r($rd_error);
	echo '<BR>';
	*/
	$status_map = getMeterRelayStatus($status_arr); // 未使用

	$sql = "SELECT dong FROM room WHERE 1 {$sql_kw} GROUP BY dong";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$dong_arr = $rs->fetchAll();

	if($floor != '') {
		$sql_kw = " AND floor = '{$floor}' GROUP BY floor";
	}
	else
	{
		$sql_kw = " GROUP BY floor= 'B1' desc , floor Asc";
	}
	
	$sql = "SELECT floor FROM room WHERE 1 {$sql_kw} ";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$floor_arr = $rs->fetchAll();
	 
	foreach($dong_arr as $d_v) 
	{
		foreach($floor_arr as $f_v)
		{
			$tail_flag = false;
			$sql = "SELECT * FROM room WHERE dong = '{$d_v['dong']}' AND floor = '{$f_v['floor']}' ORDER BY dong, floor, `name` ";
			//echo 'sql:'.$sql.'<BR>';
			$rs  = $PDOLink->prepare($sql);
			$rs->execute();
			$room_arr = $rs->fetchAll();
			
			foreach($room_arr as $v) 
			{
				$dong  = $v['dong'];
				$center_id  = $v['center_id'];
				$meter_id   = $v['meter_id'];
				$uptime     = $status_arr[$dong][$center_id]['utime'];
				$status1    = $rd_error[$dong][$center_id][$meter_id];
				$status2    = $md_error[$dong][$center_id][$meter_id];
				$status3    = $pm_error[$dong][$center_id][$meter_id];
				// $status4    = $mr_status[$center_id][$meter_id];
				
				if($o_f_v != $f_v['floor'] || $o_d_v != $d_v['dong']) {
					
					$o_f_v = $f_v['floor'];
					$o_d_v = $d_v['dong'];
					
					$tail_flag = true;
					
					$new_body .= "	<div class='my-2'>
										<h5 class='text-gray-900 font-weight-bold'>{$d_v['dong']}/{$f_v['floor']}</h5>
										<h5 class='text-gray-900 font-weight-bold'>更新時間:{$uptime}</h5>
									</div>
									<div class='row'>";
				}
				
				if(strtotime($now_time) - strtotime($uptime) > $chk_time) {
					
					$hd1_error  = "Reader<br>";
					$hd2_error  = "Meter<br>";
					$hd3_error  = "PowerMeter<br>";
					//echo "v['name']:".$v['name'].'<BR>';
					$hd1_error  = in_array($v['name'], $exclu_room) ? '' : $hd1_error;
					//echo "hd1_error:".$hd1_error.'<BR>';
											
					$new_body .= "	<div class='col-6 col-lg-2'>
									  <div class='card card-h mb-4 card-green text-green text-center h-282'>
										<div class='py-3'>
											<h4 class='m-0 font-weight-bold'>{$v['name']}：<span class='text_ng'>NG</span></h4>
										</div>
										<div>
											<p>({$center_id}/{$meter_id})</p>
											<p>({$v['amount']})</p>
										</div>
										<div>
											<span>類型</span><p class='text-left ml-4'>{$hd1_error}{$hd2_error}{$hd3_error}</p>
										</div>
									  </div>
									</div>";
				} else {
										
					if($status1 == 1 || $status2 == 1 || $status3 == 1) {	
					
						// if(in_array($v['name'], $exclu_room) & 
							// $status1 == 1 & $status2 == 1 & $status3 == 0) {
							// continue;
						// }
						
						$hd1_error  = $status1 == 1 ? "Reader<br>" : "";
						$hd2_error  = $status2 == 1 ? "Meter<br>" : "";
						$hd3_error  = $status3 == 1 ? "PowerMeter<br>" : "";
						
						$hd1_error  = in_array($v['name'], $exclu_room) ? '' : $hd1_error;
						
						$new_body .= "	<div class='col-6 col-lg-2'>
										  <div class='card card-h mb-4 card-green text-green text-center h-282'>
											<div class='py-3'>
												<h4 class='m-0 font-weight-bold'>{$v['name']}：<span class='text_ng'>NG</span></h4>
											</div>
											<div>
												<p>({$center_id}/{$meter_id})</p>
												<p>({$v['amount']})</p>
											</div>
											<div>
												<span>類型</span><p class='text-left ml-4'>{$hd1_error}{$hd2_error}{$hd3_error}</p>
											</div>
										  </div>
										</div>";
					}
				}
			}
			
			if($tail_flag) {
				$new_body .= "</div>";
			}
		}
	}

	echo $new_body;
?>       