<?php
    include('includes/header.php');
    include('includes/nav.php');
	
    include_once('model/hardwarelist_utility.php');
    
    $chk_time    = 3600; // 30 mins 
    $now_time    = date('Y-m-d H:i:s');
    
    $issue_admin = '';
    $issue_stay  = '';
    $issue_leave = '';
    $issue_room  = '';
    $issue_cust  = '';
    $issue_log   = '';
    $issue_sql   = '';
    $issue_ng    = 0;
    $issue_commu = 0;

    $sql = "SELECT * FROM room";
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
    $room_arr = $rs->fetchAll();
    $issue_room = sizeof($room_arr);

    $exclu_room = array('Q6214', 'Q6215'); // 由前端排除硬體錯誤
    $status_arr = getReaderData();
    $rd_error   = getReaderDeviceError($status_arr);
    $md_error   = getMeterDeviceError($status_arr);
    $pm_error   = getPowerMeterError($status_arr);
    //$mr_status  = getMeterRelayStatus($status_arr);

    $rd_counts  = count($status_arr);

    $rd_errors = 0;
    $md_errors = 0;
    $pm_errors = 0;
    /* 原統計NG
    foreach($room_arr as $v) 
    {
      $center_id = $v['center_id'];
      $meter_id  = $v['meter_id'];
      $uptime    = $status_arr[$center_id]['utime'];
      $status1   = $rd_error[$center_id][$meter_id];
      $status2   = $md_error[$center_id][$meter_id];
      $status3   = $pm_error[$center_id][$meter_id];
      
      if(strtotime($now_time) - strtotime($uptime) > $chk_time) {
        
        if(!in_array($v['name'], $exclu_room)) { $issue_commu++; }
        
      } else {
                  
        if($status1 == 1 || $status2 == 1 || $status3 == 1) {
          if(!in_array($v['name'], $exclu_room)) { $issue_ng++; }
        }
      }
    }
    */
    // 統計各種NG -- start
    $sql = "SELECT dong FROM room GROUP BY dong";
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
    $dong_arr = $rs->fetchAll();
//    $sql = "SELECT floor FROM room GROUP BY floor= 'B1' desc , floor asc";
    $sql = "SELECT floor FROM room GROUP BY floor ";
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
    $floor_arr = $rs->fetchAll();
    
    $old_dong = '';
    $old_center_id = '';
    $dong_1 = 1;
    $dong_2 = 1;
    foreach($dong_arr as $d_v) // Reader
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
          if(in_array($v['name'], $exclu_room)) {
            continue;
          }
          
          $dong  = $v['dong'];
          $center_id  = $v['center_id'];
          $meter_id   = $v['meter_id'];
          for($j2=1;$j2<$rd_counts;$j2++) {
              if($rd_error[$j2][$center_id][41] == $dong) {
                  $uptime     = $rd_error[$j2][$center_id][43];
                  $status    = $rd_error[$j2][$center_id][$meter_id];
              }
          }
          $show_desc  = ($status == 1) ? "NG" : "OK";
          if(strtotime($now_time) - strtotime($uptime) > $chk_time) {
            $show_desc  = "NG";
          }
          if($show_desc == "NG") {
            $rd_errors++;
            $issue_ng++;
          }
    
        }
  
      }
    }
    foreach($dong_arr as $d_v) // Meter
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
          if(in_array($v['name'], $exclu_room)) {
            continue;
          }
          
          $dong  = $v['dong'];
          $center_id  = $v['center_id'];
          $meter_id   = $v['meter_id'];
          for($j2=1;$j2<$rd_counts;$j2++) {
              if($md_error[$j2][$center_id][41] == $dong) {
                  $uptime     = $md_error[$j2][$center_id][43];
                  $status    = $md_error[$j2][$center_id][$meter_id];
              }
          }
          $show_desc  = ($status == 1) ? "NG" : "OK";
          if(strtotime($now_time) - strtotime($uptime) > $chk_time) {
            $show_desc  = "NG";
          }
          if($show_desc == "NG") {
            $md_errors++;
            $issue_ng++;
          }
        }
  
      }
    }
    foreach($dong_arr as $d_v) // PowerMeter
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
          if(in_array($v['name'], $exclu_room)) {
            continue;
          }
          
          $dong  = $v['dong'];
          $center_id  = $v['center_id'];
          $meter_id   = $v['meter_id'];
          for($j2=1;$j2<$rd_counts;$j2++) {
              if($rd_error[$j2][$center_id][41] == $dong) {
                  $uptime     = $pm_error[$j2][$center_id][43];
                  $status    = $pm_error[$j2][$center_id][$meter_id];
              }
          }
          $show_desc  = ($status == 1) ? "NG" : "OK";
          if(strtotime($now_time) - strtotime($uptime) > $chk_time) {
            $show_desc  = "NG";
          }
          if($show_desc == "NG") {
            $pm_errors++;
            $issue_ng++;
          }
        }
  
      }
    }
    // 統計各種NG -- end
    // 統計通訊錯誤
    $sql = "SELECT a.id,a.Center_id, a.update_date AS update_date_tra, a.dong,
            (SELECT floor FROM `room` WHERE center_id = a.id and dong = a.dong LIMIT 0, 1) as 'floor',
            c.TransmissionNumber as tx,c.ReceiveNumber as rx
            FROM room_hardware_status a
            LEFT JOIN room_hardware_transmission c on c.center_id=a.center_id and c.dong = a.dong";
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
    $data = $rs->fetchAll();
    if($data) {      
      foreach($data as $v) {
        $issue_commu++;
      }
    }

    $sql = "SELECT COUNT(*) as 'count' FROM `member` WHERE identity = '1'";
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
    $tmp = $rs->fetch();
    $issue_admin = $tmp['count'];

    $sql = "SELECT COUNT(*) as 'count' FROM `member` WHERE identity = '0' AND room_strings != ''";
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
    $tmp = $rs->fetch();
    $issue_stay = $tmp['count'];
    
    $sql = "SELECT COUNT(*) as 'count' FROM `member` WHERE identity = '0' AND room_strings = ''";
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
    $tmp = $rs->fetch();
    $issue_leave = $tmp['count'];
    
    $sql = "SELECT COUNT(*) as 'count' FROM `content_us` WHERE data_type = '1'";
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
    $tmp = $rs->fetch();
    $issue_cust = $tmp['count'];
    
    $sql = "SELECT COUNT(*) as 'count' FROM log_list";
    $rs  = $PDOLink->query($sql);
    $tmp = $rs->fetch();                      
    $issue_log = $tmp['count'];
    
    $sql = "SELECT COUNT(*) as 'count' FROM system_setting 
        WHERE computer_name = 'Web' OR title = '工程模式'";
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
    $tmp = $rs->fetch();
    $issue_sql = $tmp['count'];
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <!--<div class="d-sm-flex align-items-center justify-content-between mb-4">-->
            <!--<h1 class="h3 mb-0 text-gray-800 text-center">【AOTECH 弘光智慧校園】總覽</h1>-->
            <h1 class="font-weight-bold text-center">【AOTECH 北科智慧校園】</h1>
            <h1 class="mb-2 font-weight-bold text-center">總覽</h1>
        <!--</div>-->

        <!-- Content Row -->
        <div class="row">

            <!-- 客服中心 -->
            <div class="col-xl-6 col-md-6 mb-4">
              <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="mb-1 font-weight-bold text-gray-800 text-center">
                        <h5 class='text-green'>客服中心</h5>
                        <hr>
                        <div class="alert alert-danger mb-0">
                            <h4 class="mb-0 p-3 font-weight-bold"><?php echo $issue_cust ?> 個待處理問題</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- 客服中心 END -->

            <!-- 宿舍硬體系統檢測 -->
            <div class="col-xl-6 col-md-6 mb-4">
              <div class="card border-right-danger shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="mb-1 font-weight-bold text-gray-800 text-center">
                        <h5 class='text-green'>宿舍硬體系統檢測</h5>
                        <hr>
                        <div class="alert alert-danger mb-0">
                            <!--<h4 class="mb-0 p-3 font-weight-bold"><?php echo $issue_ng?> 個NG</h4>原先是總計NG數量，現在要細到逐一羅列-->
                            <p class="mb-0 p-2 font-weight-bold"><?php echo $rd_errors?> 個Reader NG</p>
                            <p class="mb-0 p-2 font-weight-bold"><?php echo $md_errors?> 個Meter NG</p>
                            <p class="mb-0 p-2 font-weight-bold"><?php echo $pm_errors?> 個PowerMeter NG</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- 宿舍硬體系統檢測 END -->

            <!-- 名單資料 -->
            <div class="col-xl-12 col-md-6 mb-4">
              <div class="card border-bottom-success border-top-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="mb-1 font-weight-bold text-gray-800 text-center">
                        <h5 class='text-green'>名單資料</h5>
                        <hr>
                        <div class="row alert alert-success">
                            <div class="col-xl-4 col-md-12">
                                <h4 class="mb-0 p-3 font-weight-bold"><?php echo $issue_admin ?> 位</h4>
                                <h4 class="mb-0 p-3 font-weight-bold">管理員</h4>
                                <hr class="d-xl-none">
                            </div>
                            <div class="col-xl-4 col-md-12">
                                <h4 class="mb-0 p-3 font-weight-bold"><?php echo $issue_stay ?> 位</h4>
                                <h4 class="mb-0 p-3 font-weight-bold">現有住宿生</h4>
                                <hr class="d-xl-none">
                            </div>

                            <div class="col-xl-4 col-md-12">
                                <h4 class="mb-0 p-3 font-weight-bold"><?php echo $issue_leave ?> 位</h4>
                                <h4 class="mb-0 p-3 font-weight-bold">非住宿生</h4>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- 名單資料 END -->

            <!-- 房間總數&系統檢測通訊狀況 -->
            <div class="col-xl-6 col-md-6 mb-4">
              <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="mb-1 font-weight-bold text-gray-800 text-center">
                        <h5 class='text-green'>房間總數</h5>
                        <hr>
                        <div class="alert alert-info mb-0">
                            <h4 class="mb-0 p-3 font-weight-bold"><?php echo $issue_room ?>間</h4>
                        </div>
                      </div>
                      <br>
                      <div class="mb-1 font-weight-bold text-gray-800 text-center">
                        <h5 class='text-green'>系統檢測通訊狀況</h5>
                        <hr>
                        <div class="alert alert-danger mb-0">
                            <h4 class="mb-0 p-3 font-weight-bold"><?php echo $issue_commu ?> 個 error</h4>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- 房間總數&系統檢測通訊狀況 END -->


            <!-- Log -->
            <div class="col-xl-6 col-md-6 mb-4">
              <div class="card border-right-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="mb-1 font-weight-bold text-gray-800 text-center">
                        <h5 class='text-green'>Log</h5>
                        <hr>
                        <div class="alert alert-info mb-0">
                            <h4 class="mb-0 p-3 font-weight-bold">前台 Log</h4>
                            <h4 class="mb-0 p-3 font-weight-bold"><?php echo $issue_log ?>筆</h4>
                            <hr>
                            <h4 class="mb-0 p-3 font-weight-bold">SQL Log</h4>
                            <h4 class="mb-0 p-3 font-weight-bold"><?php echo $issue_sql ?>筆</h4>

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Log END -->



        </div>
        <!-- Content Row -->

    </div>
    <!-- /.container-fluid -->

<?php
    include('includes/scripts.php');
    include('includes/footer.php');
?>