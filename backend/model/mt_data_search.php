<?php 

	include_once('../../config/db.php');

	include_once('hardwarelist_utility.php');

	 include('../chk_log_in.php');

	$chk_time = 1800; // 30 mins 
	$now_time = date('Y-m-d H:i:s');
	
	$sql_kw   = "";
	$hd_type  = "Meter 110";
	$hd_type_220  = "Meter 220";
	
	$floor    = $_POST['floor'];

	if($floor != '') {
		$sql_kw = " AND floor = '{$floor}'";
	}

	$status_arr = getReaderData();
	$status_map = getMeterDeviceError($status_arr);
	$status_map_220 = getMeterDeviceError220($status_arr);

	$rd_counts  = count($status_arr);
	$rd_counts_220  = count($status_arr);

	$sql = "SELECT dong FROM room WHERE 1 {$sql_kw} GROUP BY dong";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$dong_arr = $rs->fetchAll();

	if($floor != '') {
		$sql_kw = " AND floor = '{$floor}' GROUP BY floor";
	}
	else
	{
	//	$sql_kw = " GROUP BY floor= 'B1' desc , floor Asc";
		$sql_kw = " GROUP BY floor";
	}
	
	$sql = "SELECT floor FROM room WHERE 1 {$sql_kw}";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$floor_arr = $rs->fetchAll();
	
	foreach($dong_arr as $d_v) 
	{
		foreach($floor_arr as $f_v)
		{
			$tail_flag = false;
			$sql = "SELECT * FROM room WHERE dong = '{$d_v['dong']}' AND floor = '{$f_v['floor']}' ORDER BY floor,center_id, meter_id, `name` ";
			$rs  = $PDOLink->prepare($sql);
			$rs->execute();
			$room_arr = $rs->fetchAll();
			
			foreach($room_arr as $v) 
			{
				$dong  = $v['dong'];
				$center_id  = $v['center_id'];
				$meter_id   = $v['meter_id'];
                for($j2=1;$j2<$rd_counts;$j2++) {
                    if($status_map[$j2][$center_id][41] == $dong) {
                        
                        //echo 'j2:'.$j2.',';
                        //echo 'center_id:'.$center_id.'<BR>';
                        //print_r($status_map[$j2]);
                        //echo '<BR>';
                        
                        $uptime     = $status_map[$j2][$center_id][43];
                        $status    = $status_map[$j2][$center_id][$meter_id];
                        //echo 'uptime:'.$uptime.'<BR>';
                    }
                }
				//$uptime     = $status_arr[$center_id]['utime'];
				//$status     = $status_map[$center_id][$meter_id];
				
				$show_desc  = ($status == 1) ? "NG" : "OK"; // 0:NG 1:OK
				$show_class = ($status == 1) ? "text_ng" : "text_normal";
				
				if(strtotime($now_time) - strtotime($uptime) > $chk_time) {
					$show_desc  = "NG";
					$show_class = "text_ng";
				} else {
					$show_desc  = ($status == 1) ? "NG" : "OK";
					$show_class = ($status == 1) ? "text_ng" : "text_normal";
				}
				for($j2=1;$j2<$rd_counts_220;$j2++) {
                    if($status_map_220[$j2][$center_id][41] == $dong) {
                        
                        //echo 'j2:'.$j2.',';
                        //echo 'center_id:'.$center_id.'<BR>';
                        //print_r($status_map_220[$j2]);
                        //echo '<BR>';
                        
                        $uptime     = $status_map_220[$j2][$center_id][43];
                        $status    = $status_map_220[$j2][$center_id][$meter_id];
                        //echo 'uptime:'.$uptime.'<BR>';
                    }
                }
				
				$show_desc_220  = ($status == 1) ? "NG" : "OK"; // 0:NG 1:OK
				$show_class_220 = ($status == 1) ? "text_ng" : "text_normal";
				
				if(strtotime($now_time) - strtotime($uptime) > $chk_time) {
					$show_desc_220  = "NG";
					$show_class_220 = "text_ng";
				} else {
					$show_desc_220  = ($status == 1) ? "NG" : "OK";
					$show_class_220 = ($status == 1) ? "text_ng" : "text_normal";
				}
				
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
				
				$new_body .= "	<div class='col-6 col-lg-2'>
								  <div class='card card-h mb-4 card-green text-green text-center h-282'>
									<div class='py-3'>
										<h4 class='m-0 font-weight-bold'>{$v['name']}：<span class='{$show_class}'>{$show_desc}(110)</span></h4>
										<h4 class='m-0 font-weight-bold'>{$v['name']}：<span class='{$show_class_220}'>{$show_desc_220}(220)</span></h4>
									</div>
									<div>
										<p>({$center_id}/{$meter_id})</p>
										<p>({$v['amount']})</p>
									</div>
									<div>
										<span>類型</span>
										<p class='text-left ml-4'>{$hd_type}</p>
										<p class='text-left ml-4'>{$hd_type_220}</p>
									</div>
								  </div>
								</div>";
			}
			
			if($tail_flag) {
				$new_body .= "</div>";
			}
		}
	}

	echo $new_body;
?>       