<?php
	function getRoomData() 
	{
		$room_arr;
		$new_link = db_conn();

		$sql = "SELECT * FROM room ORDER BY center_id, meter_id, `name`";
		$rs  = $new_link->prepare($sql);
		$rs->execute();
		$rs_data = $rs->fetchAll();
		
		foreach($rs_data as $v) {
			
			$center_id = $v['center_id'];
			$meter_id  = $v['meter_id'];
			$room_arr[$center_id][$meter_id] = $v;
		}
		
		return $room_arr;
	}

	function getReaderData() 
	{
		$status_arr;
		$new_link = db_conn();

		$sql = "SELECT * FROM room_hardware_status ORDER BY dong,Center_id";
		$rs  = $new_link->prepare($sql);
		$rs->execute();
		$rs_data = $rs->fetchAll();
		
		foreach($rs_data as $v) 
		{			
			$dong = $v['dong'];
			$center_id = $v['Center_id'];
			$status_ww = $v['status'];
			$upd_time  = $v['update_date'];
		
			$tmp1 = array();
			$tmp2 = array();
			$tmp3 = array();
			$data = json_decode($status_ww);
			//echo 'status_ww:'.$status_ww.'<BR>';
			foreach($data as $k => $w) 
			{
				
				switch($k) {
					
					case 5:
					case 6:
					case 7:
					case 8:
					
						$tmp_bin = str_pad(decbin($w), 8, '0', STR_PAD_LEFT);
						//echo 'dong:'.$v['dong'].',';
						//echo 'center_id:'.$v['Center_id'].',';
						//echo 'tmp_bin:'.$tmp_bin.'<BR>';
						$arr_bin = preg_split('//', $tmp_bin, -1, PREG_SPLIT_NO_EMPTY);
						
						for($i = 0; $i < sizeof($arr_bin); $i++) {
							$tmp1[] = $arr_bin[$i];
						}
						
						break;
					case 10:
					case 11:
					case 12:
					case 13:
					
						$tmp_bin = str_pad(decbin($w), 8, '0', STR_PAD_LEFT);
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
			$status_arr[$dong][$center_id]['ReaderDeviceError'] = $tmp1;
			$status_arr[$dong][$center_id]['MeterDeviceError']  = $tmp2;
			$status_arr[$dong][$center_id]['PowerMeterError']   = $tmp3;
			$status_arr[$dong][$center_id]['MeterRelayStatus']  = $tmp4;
			$status_arr[$dong][$center_id]['utime'] = $upd_time;
		}
		
		return $status_arr;
	}

	function getReaderDeviceError($status_arr) 
	{
		$result;
		
		foreach($status_arr as $k => $v) {
			
			$tmp1 = $v['ReaderDeviceError'];
			
			for($i = sizeof($tmp1), $j=1; $i > 0; $j++) {
				$result[$k][$j] = $tmp1[--$i];
			}	
		}
		
		return $result;
	}

	function getMeterDeviceError($status_arr) 
	{	
		$result;

		foreach($status_arr as $k => $v) {
			
			$tmp2 = $v['MeterDeviceError'];
			
			for($i = sizeof($tmp2), $j=1; $i > 0; $j++) {
				$result[$k][$j] = $tmp2[--$i];
			}
		}
		
		return $result;
	}
	
	function getPowerMeterError($status_arr) 
	{
		$result;

		foreach($status_arr as $k => $v) {
			
			$tmp3 = $v['PowerMeterError'];
			
			for($i = sizeof($tmp3), $j=1; $i > 0; $j++) {
				$result[$k][$j] = $tmp3[--$i];
			}
		}
		
		return $result;
	}
	
	function getMeterRelayStatus($status_arr) 
	{
		$result;

		foreach($status_arr as $k => $v) {
			
			$tmp4 = $v['MeterRelayStatus'];
			
			for($i = sizeof($tmp4), $j=1; $i > 0; $j++) {
				$result[$k][$j] = $tmp4[--$i];
			}
		}
		
		return $result;
	}
?>