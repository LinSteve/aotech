<?php
  include('includes/header.php');
  include('includes/nav.php');

  $chk_time = 1800; // 30 mins 
  $now_time = date('Y-m-d H:i:s');
  /* 原語句只能撈取centerid是唯一值的
  $sql = "SELECT *, (SELECT dong FROM `room` WHERE center_id = rhs.id LIMIT 0, 1) as 'dong', 
    (SELECT floor FROM `room` WHERE center_id = rhs.id LIMIT 0, 1) as 'floor',
    (SELECT update_date FROM `room_hardware_transmission` WHERE center_id = rhs.id LIMIT 0, 1) as update_date_tra,
    (SELECT TransmissionNumber FROM `room_hardware_transmission` WHERE center_id = rhs.id LIMIT 0, 1) as 'tx', 
    (SELECT ReceiveNumber FROM `room_hardware_transmission` WHERE center_id = rhs.id LIMIT 0, 1) as 'rx' 
    FROM `room_hardware_status` rhs ORDER BY rhs.id";
  */
  $sql = "SELECT a.id,a.Center_id, c.update_date AS update_date_tra, a.dong,
          (SELECT floor FROM `room` WHERE center_id = a.Center_id and dong = a.dong LIMIT 0, 1) as 'floor',
          c.TransmissionNumber as tx,c.ReceiveNumber as rx
          FROM room_hardware_status a
          LEFT JOIN room_hardware_transmission c on c.center_id=a.center_id and c.dong = a.dong
          ORDER BY a.id";
  //echo 'sql:'.$sql.'<BR>';
  $rs  = $PDOLink->prepare($sql);
  $rs->execute();
  $data = $rs->fetchAll();
?>

  <!-- Begin Page Content -->
  <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="mb-2 font-weight-bold">系統檢測通訊狀況</h1>
          
          <!--
          <p class="text-lg text-center font-weight-bold NG-color">
            叮嚀：表格資料帶出全部，每個CenterID只會有一筆資料在做更新。
            <br>資料排序依照資料庫排列的順序。
          <br>更新時間與當下時間差30 min顯示NG-color的顏色</p>
          -->

          <!-- Search Card  
          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header text-center">檢視系統狀態</div>
                <div class="card-body">

                  <p class="text-lg text-center font-weight-bold NG-color">叮嚀：icon按鈕點擊後，帶出相對應的資料！  初始表格資料帶出全部。資料排序依據更新時間</p>

                  <ul id="icon_nav_h" class="top_ico_nav clearfix">
                      <li class="col-lg-3 col-sm-6 my-2">
                        <a href="#" class="active">
                          <i class="fas fa-exclamation-triangle fa-6x"></i>
                          <span class="menu_label">Meter</span>
                        </a>
                      </li>
                      <li class="col-lg-3 col-sm-6 my-2">             
                        <a href="#">
                          <i class="fas fa-exclamation-triangle fa-6x"></i>
                          <span class="menu_label">Reader</span>
                        </a>
                      </li>
                      <li class="col-lg-3 col-sm-6 my-2">             
                        <a href="#">
                          <i class="fas fa-exclamation-triangle fa-6x"></i>
                          <span class="menu_label">Reader&Meter</span>
                        </a>
                      </li>
                      <li class="col-lg-3 col-sm-6 my-2">             
                        <a href="#">
                          <i class="fas fa-exclamation-triangle fa-6x"></i>
                          <span class="menu_label">NG</span>
                        </a>
                      </li>
                    </ul>
                    
                </div>
              </div>
            </div>

          </div>
          -->
          <!-- Search Card END -->

          <!--Table--->
          <div class="table-responsive">
                      <table class="table  text-center font-weight-bold">
                        <thead class="thead-green">
                        <tr class="text-center">
                          <th scope="col">CenterID</th> 
                          <th scope="col">更新時間</th>
                          <th scope="col">棟別/樓層</th>
                          <th scope="col">傳輸筆數</th>
                          <th scope="col">接收筆數</th>
                        </tr>
                        </thead>
                        <tbody>
						<?php
						foreach($data as $v) 
						{
							$td_style = (strtotime($now_time) - strtotime($v['update_date_tra']) > $chk_time) ? 'text_ng' : 'text_normal';
						?>	
                          <tr>
                            <td class='<?php echo $td_style?>'><?php echo $v['id'] ?></td>
                            <td class='<?php echo $td_style?>'><?php echo $v['update_date_tra'] ?></td>
                            <td class='<?php echo $td_style?>'><?php echo $v['dong'].' / '.$v['floor'] ?></td>
                            <td class='<?php echo $td_style?>'><?php echo $v['tx'] ?></td>
                            <td class='<?php echo $td_style?>'><?php echo $v['rx'] ?></td>
                          </tr>                     
						<?php
						}
						?>
                        </tbody>
                      </table>
          </div>


  </div>
  <!-- /.container-fluid -->


<?php
    include('includes/scripts.php');
    include('includes/footer.php');
?>