<?php 

	include_once('../../config/db.php');

	$status_arr = getReaderData();
	function getReaderData() 
	{
		$status_arr;
		$new_link = db_conn();

		$sql = "SELECT * FROM room_hardware_status ORDER BY dong,Center_id";
		$rs  = $new_link->prepare($sql);
		$rs->execute();
		$rs_data = $rs->fetchAll();
		/*
			echo 'rs_data:<BR>';
			print_r($rs_data);
			echo '<BR>';
		*/

		$m = 1;		
		foreach($rs_data as $v) 
		{			
			$dong = $v['dong'];
			$sql = "SELECT * FROM room WHERE dong = '{$dong}'";
			$rs  = $new_link->prepare($sql);
			$rs->execute();
			$dong_data = $rs->fetchAll();
			if($dong_data) { // room表裡面有的資料才撈出來
				$center_id = $v['Center_id'];
				$status_ww = $v['status'];
				$upd_time  = $v['update_date'];
			
				$tmp1 = array();
				$tmp2 = array();
				$tmp3 = array();			
				$tmp1a = "";
				$tmp2a = "";
				$tmp3a = "";			
				$tmp4a = "";			
				$data = json_decode($status_ww);
				/*
				echo 'data_status:<BR>';
				print_r($data);
				echo '<BR>';
				*/
				//echo 'dong:'.$dong.'<BR>';
				foreach($data as $k => $w) 
				{
					
					switch($k) {
						
						case 5:
						case 6:
						case 7:
						case 8:
						
							$tmp_bin = str_pad(decbin($w), 8, '0', STR_PAD_LEFT);
							$tmp1a .= $tmp_bin;
							/*
							echo 'dong:'.$v['dong'].',';
							echo 'center_id:'.$v['Center_id'].',';
							echo 'tmp_bin:'.$tmp_bin.'<BR>';
							*/
							$arr_bin = preg_split('//', $tmp_bin, -1, PREG_SPLIT_NO_EMPTY);
							//$tmp1a = $arr_bin;
							
							for($i = 0; $i < sizeof($arr_bin); $i++) {
								$tmp1[] = $arr_bin[$i];
							}
							/*
							echo 'tmp1:<BR>';
							print_r($tmp1);
							echo '<BR>';
							*/
							
							break;
						case 10:
						case 11:
						case 12:
						case 13:
						
							$tmp_bin = str_pad(decbin($w), 8, '0', STR_PAD_LEFT);
							$tmp2a .= $tmp_bin;
							$arr_bin = preg_split('//', $tmp_bin, -1, PREG_SPLIT_NO_EMPTY);
							
							for($i = 0; $i < sizeof($arr_bin); $i++) {
								$tmp2[] = $arr_bin[$i];
							}
							
							break;
						case 14:
						case 15:
						case 16:
						case 17:
						
							$tmp_bin = str_pad(decbin($w), 8, '0', STR_PAD_LEFT);
							$tmp3a .= $tmp_bin;
							$arr_bin = preg_split('//', $tmp_bin, -1, PREG_SPLIT_NO_EMPTY);
							
							for($i = 0; $i < sizeof($arr_bin); $i++) {
								$tmp3[] = $arr_bin[$i];
							}
							
							break;
						case 22:
						case 23:
						case 24:
						case 25:
						
							$tmp_bin = str_pad(decbin($w), 8, '0', STR_PAD_LEFT);
							$tmp4a .= $tmp_bin;
							$arr_bin = preg_split('//', $tmp_bin, -1, PREG_SPLIT_NO_EMPTY);
							
							for($i = 0; $i < sizeof($arr_bin); $i++) {
								$tmp4[] = $arr_bin[$i];
							}
							
							break;
						default:
							break;
					}
				}
				/*
				$status_arr[$center_id]['ReaderDeviceError'] = $tmp1;
				$status_arr[$center_id]['MeterDeviceError']  = $tmp2;
				$status_arr[$center_id]['PowerMeterError']   = $tmp3;
				$status_arr[$center_id]['MeterRelayStatus']  = $tmp4;
				$status_arr[$center_id]['utime'] = $upd_time;
				*/
				/*
				echo 'temp1:<BR>';
				print_r($tmp1);
				echo '<BR>';
				*/
				
				$status_arr[$m][$center_id]['dong'] = $dong;
				$status_arr[$m][$center_id]['center_id'] = $center_id;
				$status_arr[$m][$center_id]['ReaderDeviceError'] = $tmp1;
				$status_arr[$m][$center_id]['MeterDeviceError']  = $tmp2;
				$status_arr[$m][$center_id]['PowerMeterError']   = $tmp3;
				$status_arr[$m][$center_id]['MeterRelayStatus']  = $tmp4;
				$status_arr[$m][$center_id]['utime'] = $upd_time;
				/*
				//echo 'm:'.$m.'<BR>';
				echo 'dong:<font color="red">'.$dong.'</font>,';
				echo 'center_id:<font color="red">'.$center_id.'</font>,';
				echo 'utime:<font color="red">'.$upd_time.'</font><BR>';
				//echo 'ReaderDeviceError:<BR>';
				//print_r($tmp1);
				//echo '<BR>';
				echo 'ReaderDeviceError:'.$tmp1a.'<BR>';
				//echo 'MeterDeviceError:<BR>';
				//print_r($tmp2);
				//echo '<BR>';
				echo 'MeterDeviceError:'.$tmp2a.'<BR>';
				//echo 'PowerMeterError:<BR>';
				//print_r($tmp3);
				//echo '<BR>';
				echo 'PowerMeterError:'.$tmp3a.'<BR>';
				//echo 'MeterRelayStatus:<BR>';
				//print_r($tmp4);
				echo 'MeterRelayStatus:'.$tmp4a.'<BR>';
				//echo '<BR>';
				echo '<BR>';
				//echo 'MeterDeviceError:'.$tmp2.'<BR>';
				//echo 'PowerMeterError:'.$tmp3.'<BR>';
				//echo 'MeterRelayStatus:'.$tmp4.'<BR>';
				*/
				$m++;
			}
						
		}	
		/*	
		echo 'status_arr:<BR>';
		print_r($status_arr);
		echo '<BR>';
		*/
		//echo 'm:'.$m.'<BR>';
	return $status_arr;
	}
