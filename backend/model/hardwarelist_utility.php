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
				$tmp5 = array();			
				$tmp1a = "";
				$tmp2a = "";
				$tmp3a = "";			
				$tmp4a = "";			
				$tmp5a = "";			
				$data = json_decode($status_ww);
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
						case 28:
						case 29:
						case 30:
						case 31:
						
							$tmp_bin = str_pad(decbin($w), 8, '0', STR_PAD_LEFT);
							$tmp5a .= $tmp_bin;
							$arr_bin = preg_split('//', $tmp_bin, -1, PREG_SPLIT_NO_EMPTY);
							
							for($i = 0; $i < sizeof($arr_bin); $i++) {
								$tmp5[] = $arr_bin[$i];
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
				$status_arr[$m][$center_id]['MeterDeviceError_220']  = $tmp5;
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
				//echo 'MeterDeviceError:<BR>';
				//print_r($tmp2);
				//echo '<BR>';
				echo 'MeterDeviceError_220:'.$tmp5a.'<BR>';
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

	function getReaderDeviceError($status_arr) 
	{
		$result;
		$now_dong = '';
		$now_center_id = '';
		$i3 = 1;
		foreach($status_arr as $k => $v) {
            /*
            echo 'v:<BR>';
            print_r($v);
            echo '<BR>';
            */			

			$i3P = $status_arr[$k][$i3]['center_id'];
            $utime = $status_arr[$k][$i3]['utime'];
            if(!$i3P) {
                $i3=1;
                $i3P = $status_arr[$k][$i3]['center_id'];
                $utime = $status_arr[$k][$i3]['utime'];
            }
			//echo 'i3P:'.$i3P.'<BR>';
			$now_dong = $v[$i3]['dong'];
			$now_center_id = $v[$i3]['center_id'];
            /*
			echo 'i2:'.$i2.'<BR>';
			echo 'i3:'.$i3.'<BR>';
			echo 'k:'.$k.'<BR>';
			echo 'old_dong:'.$old_dong.'<BR>';
			echo 'now_dong:'.$v[$k]['dong'].'<BR>';
            */
			//echo 'dong:'.$v[$i3]['dong'].'<BR>';
			//echo 'old_center_id:'.$old_center_id.'<BR>';
			//echo 'center_id:'.$v[$i3]['center_id'].'<BR>';
			//echo 'utime:'.$utime.'<BR>';
            
			$tmp1 = $v[$i3]['ReaderDeviceError'];
			//$tmp1 = $v['dong']['ReaderDeviceError'];
			/*
			echo 'tmp1:<BR>';
			print_r($tmp1);
			echo '<BR><BR>';
			*/	
			for($i = sizeof($tmp1), $j=1; $i > 0; $j++) {
				//$result[$i2][$k+1][$j] = $tmp1[--$i];
                $result[$k][$i3P][$j] = $tmp1[--$i];
			}
            $result[$k][$i3P][41] = $now_dong;
            $result[$k][$i3P][42] = $i3P; // center_id
            $result[$k][$i3P][43] = $utime; // center_id
            /*
            echo 'result check:<BR>';
            print_r($result[1]);
            echo '<BR>';
            */
            //$old_dong = $now_dong;
			//$old_center_id = $now_center_id;
			$i3++;
		}
        /*
		echo 'result 1:<BR>';
		print_r($result[1]);
		echo '<BR>';
		
		echo 'result 2:<BR>';
		print_r($result[40]);
		echo '<BR>';
		
        */
		return $result;
	}

	function getMeterDeviceError($status_arr) 
	{	
		$result;
        /*
		foreach($status_arr as $k => $v) {
			
			$tmp2 = $v['MeterDeviceError'];
			
			for($i = sizeof($tmp2), $j=1; $i > 0; $j++) {
				$result[$k][$j] = $tmp2[--$i];
			}
		}
		*/
        //echo 'getMeterDeviceError<BR>';
		$now_dong = '';
		$now_center_id = '';
		$i3 = 1;
		foreach($status_arr as $k => $v) {
            /*
            echo 'v:<BR>';
            print_r($v);
            echo '<BR>';
            */			

			$i3P = $status_arr[$k][$i3]['center_id'];
            $utime = $status_arr[$k][$i3]['utime'];
            if(!$i3P) {
                $i3=1;
                $i3P = $status_arr[$k][$i3]['center_id'];
                $utime = $status_arr[$k][$i3]['utime'];
            }
			//echo 'i3P:'.$i3P.'<BR>';
			$now_dong = $v[$i3]['dong'];
			$now_center_id = $v[$i3]['center_id'];
            /*
			echo 'i2:'.$i2.'<BR>';
			echo 'i3:'.$i3.'<BR>';
			echo 'k:'.$k.'<BR>';
			echo 'old_dong:'.$old_dong.'<BR>';
			echo 'now_dong:'.$v[$k]['dong'].'<BR>';
            */
			//echo 'dong:'.$v[$i3]['dong'].',';
			//echo 'old_center_id:'.$old_center_id.'<BR>';
			//echo 'center_id:'.$v[$i3]['center_id'].',';
			//echo 'utime:'.$utime.'<BR>';
            
			$tmp1 = $v[$i3]['MeterDeviceError'];
			//$tmp1 = $v['dong']['ReaderDeviceError'];
			
			//echo 'tmp2:<BR>';
			//print_r($tmp1);
			//echo '<BR><BR>';
				
			for($i = sizeof($tmp1), $j=1; $i > 0; $j++) {
				//$result[$i2][$k+1][$j] = $tmp1[--$i];
                $result[$k][$i3P][$j] = $tmp1[--$i];
			}
            $result[$k][$i3P][41] = $now_dong;
            $result[$k][$i3P][42] = $i3P; // center_id
            $result[$k][$i3P][43] = $utime; // center_id
            /*
            echo 'result check:<BR>';
            print_r($result[1]);
            echo '<BR>';
            */
            //$old_dong = $now_dong;
			//$old_center_id = $now_center_id;
			$i3++;
		}        
		return $result;
	}
	function getMeterDeviceError220($status_arr) 
	{	
		$result;
        /*
		foreach($status_arr as $k => $v) {
			
			$tmp2 = $v['MeterDeviceError'];
			
			for($i = sizeof($tmp2), $j=1; $i > 0; $j++) {
				$result[$k][$j] = $tmp2[--$i];
			}
		}
		*/
        //echo 'getMeterDeviceError<BR>';
		$now_dong = '';
		$now_center_id = '';
		$i3 = 1;
		foreach($status_arr as $k => $v) {
            /*
            echo 'v:<BR>';
            print_r($v);
            echo '<BR>';
            */			

			$i3P = $status_arr[$k][$i3]['center_id'];
            $utime = $status_arr[$k][$i3]['utime'];
            if(!$i3P) {
                $i3=1;
                $i3P = $status_arr[$k][$i3]['center_id'];
                $utime = $status_arr[$k][$i3]['utime'];
            }
			//echo 'i3P:'.$i3P.'<BR>';
			$now_dong = $v[$i3]['dong'];
			$now_center_id = $v[$i3]['center_id'];
            /*
			echo 'i2:'.$i2.'<BR>';
			echo 'i3:'.$i3.'<BR>';
			echo 'k:'.$k.'<BR>';
			echo 'old_dong:'.$old_dong.'<BR>';
			echo 'now_dong:'.$v[$k]['dong'].'<BR>';
            */
			//echo 'dong:'.$v[$i3]['dong'].',';
			//echo 'old_center_id:'.$old_center_id.'<BR>';
			//echo 'center_id:'.$v[$i3]['center_id'].',';
			//echo 'utime:'.$utime.'<BR>';
            
			$tmp1 = $v[$i3]['MeterDeviceError_220'];
			//$tmp1 = $v['dong']['ReaderDeviceError'];
			
			//echo 'tmp2:<BR>';
			//print_r($tmp1);
			//echo '<BR><BR>';
				
			for($i = sizeof($tmp1), $j=1; $i > 0; $j++) {
				//$result[$i2][$k+1][$j] = $tmp1[--$i];
                $result[$k][$i3P][$j] = $tmp1[--$i];
			}
            $result[$k][$i3P][41] = $now_dong;
            $result[$k][$i3P][42] = $i3P; // center_id
            $result[$k][$i3P][43] = $utime; // center_id
            /*
            echo 'result check:<BR>';
            print_r($result[1]);
            echo '<BR>';
            */
            //$old_dong = $now_dong;
			//$old_center_id = $now_center_id;
			$i3++;
		}        
		return $result;
	}
	
	function getPowerMeterError($status_arr) 
	{
		$result;
		/*
		foreach($status_arr as $k => $v) {
			
			$tmp3 = $v['PowerMeterError'];
			
			for($i = sizeof($tmp3), $j=1; $i > 0; $j++) {
				$result[$k][$j] = $tmp3[--$i];
			}
		}
		*/
		$now_dong = '';
		$now_center_id = '';
		$i3 = 1;
		foreach($status_arr as $k => $v) {
            /*
            echo 'v:<BR>';
            print_r($v);
            echo '<BR>';
            */			

			$i3P = $status_arr[$k][$i3]['center_id'];
            $utime = $status_arr[$k][$i3]['utime'];
            if(!$i3P) {
                $i3=1;
                $i3P = $status_arr[$k][$i3]['center_id'];
                $utime = $status_arr[$k][$i3]['utime'];
            }
			//echo 'i3P:'.$i3P.'<BR>';
			$now_dong = $v[$i3]['dong'];
			$now_center_id = $v[$i3]['center_id'];
            /*
			echo 'i2:'.$i2.'<BR>';
			echo 'i3:'.$i3.'<BR>';
			echo 'k:'.$k.'<BR>';
			echo 'old_dong:'.$old_dong.'<BR>';
			echo 'now_dong:'.$v[$k]['dong'].'<BR>';
            */
			//echo 'dong:'.$v[$i3]['dong'].',';
			//echo 'old_center_id:'.$old_center_id.'<BR>';
			//echo 'center_id:'.$v[$i3]['center_id'].',';
			//echo 'utime:'.$utime.'<BR>';
            
			$tmp3 = $v[$i3]['PowerMeterError'];
			//$tmp1 = $v['dong']['ReaderDeviceError'];
			
			//echo 'tmp2:<BR>';
			//print_r($tmp1);
			//echo '<BR><BR>';
				
			for($i = sizeof($tmp3), $j=1; $i > 0; $j++) {
				//$result[$i2][$k+1][$j] = $tmp1[--$i];
                $result[$k][$i3P][$j] = $tmp3[--$i];
			}
            $result[$k][$i3P][41] = $now_dong;
            $result[$k][$i3P][42] = $i3P; // center_id
            $result[$k][$i3P][43] = $utime; // center_id
            /*
            echo 'result check:<BR>';
            print_r($result[1]);
            echo '<BR>';
            */
            //$old_dong = $now_dong;
			//$old_center_id = $now_center_id;
			$i3++;
		}        
		return $result;
	}
	
	function getMeterRelayStatus($status_arr) 
	{
		$result;
		/*
		foreach($status_arr as $k => $v) {
			
			$tmp4 = $v['MeterRelayStatus'];
			
			for($i = sizeof($tmp4), $j=1; $i > 0; $j++) {
				$result[$k][$j] = $tmp4[--$i];
			}
		}
		*/

		$now_dong = '';
		$now_center_id = '';
		$i3 = 1;
		foreach($status_arr as $k => $v) {
            /*
            echo 'v:<BR>';
            print_r($v);
            echo '<BR>';
            */			

			$i3P = $status_arr[$k][$i3]['center_id'];
            $utime = $status_arr[$k][$i3]['utime'];
            if(!$i3P) {
                $i3=1;
                $i3P = $status_arr[$k][$i3]['center_id'];
                $utime = $status_arr[$k][$i3]['utime'];
            }
			//echo 'i3P:'.$i3P.'<BR>';
			$now_dong = $v[$i3]['dong'];
			$now_center_id = $v[$i3]['center_id'];
            /*
			echo 'i2:'.$i2.'<BR>';
			echo 'i3:'.$i3.'<BR>';
			echo 'k:'.$k.'<BR>';
			echo 'old_dong:'.$old_dong.'<BR>';
			echo 'now_dong:'.$v[$k]['dong'].'<BR>';
            */
			//echo 'dong:'.$v[$i3]['dong'].',';
			//echo 'old_center_id:'.$old_center_id.'<BR>';
			//echo 'center_id:'.$v[$i3]['center_id'].',';
			//echo 'utime:'.$utime.'<BR>';
            
			$tmp3 = $v[$i3]['MeterRelayStatus'];
			//$tmp1 = $v['dong']['ReaderDeviceError'];
			
			//echo 'tmp2:<BR>';
			//print_r($tmp1);
			//echo '<BR><BR>';
				
			for($i = sizeof($tmp3), $j=1; $i > 0; $j++) {
				//$result[$i2][$k+1][$j] = $tmp1[--$i];
                $result[$k][$i3P][$j] = $tmp3[--$i];
			}
            $result[$k][$i3P][41] = $now_dong;
            $result[$k][$i3P][42] = $i3P; // center_id
            $result[$k][$i3P][43] = $utime; // center_id
            /*
            echo 'result check:<BR>';
            print_r($result[1]);
            echo '<BR>';
            */
            //$old_dong = $now_dong;
			//$old_center_id = $now_center_id;
			$i3++;
		}        

		return $result;
	}
?>