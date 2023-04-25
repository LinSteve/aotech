<?php
    include('includes/header.php');
    include('includes/nav.php');
	include('includes/scripts.php');

if(isset($_SESSION['admin_user']['id']) && !empty($_SESSION['admin_user']['id']))
{
	$pagesize = 10;
	$dong_arr  = array();
	$floor_arr = array();
	
	$sql_kw    = '';
	$door      = $_GET['door'];
	$serach    = $_GET['serach'];
	
	# 搜尋
	//if($serach  == '1')
	//{ 
		$condition = $_GET['condition'];
		//echo $condition ;
		//exit();
		 
		// 硬體跳頁計算
		if($_GET['condition'] == "") // 無條件
		{
			$sql_count = "SELECT COUNT(*) as 'count' FROM reset_hardware_info ";
		}else
		{
			$sql_kw = " AND `description` LIKE '%".trim($_GET['condition'])."%' ";
			//$sql_count = "SELECT COUNT(*) as 'count' FROM reset_hardware_info WHERE 1 ".$sql_kw;
			$sql_count ="
				SELECT COUNT(*) as 'count'
				FROM reset_hardware_info 
				WHERE 1 ".$sql_kw."ORDER BY `id`
			";
		}

		$stmt  = $PDOLink->query($sql_count);
		$tmp = $stmt->fetch();                      
		$rownum = $tmp['count']; 
		
		if(isset($_GET['page'])) {
			$page = $_GET['page'];  
		} else {
			$page = 1;                                 
		}
		
		$pageurl  = '';
		$pagenum  = (int) ceil($rownum / $pagesize);  
		$prepage  = $page - 1;                        
		$nextpage = $page + 1;
		
		if($page == 1) {                         
			$pageurl .= " ".$lang->line("index.home")." | ".$lang->line("index.previous_page")." | ";
		} else {
			$pageurl .= "<a href='?page=1&room_num={$room_num}&mode={$mode}&serach={$serach}'>".$lang->line("index.home").
						"</a> | <a href='?page={$prepage}&room_num={$room_num}&mode={$mode}&serach={$serach}'>".$lang->line("index.previous_page")."</a> | ";
		}
		
		if($page == $pagenum || $pagenum == 0) {     
			$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
		} else {
			$pageurl .= "<a href='?page={$nextpage}&room_num={$room_num}&mode={$mode}&serach={$serach}'>".$lang->line("index.next_page").
						"</a> | <a href='?page={$pagenum}&room_num={$room_num}&mode={$mode}&serach={$serach}'>".$lang->line("index.last_page")."</a>";
		}
		
		// 硬體重啟SQL
		if($_GET['condition'] == "") // 無條件
		{
			$sql_page = "SELECT * FROM `reset_hardware_info` WHERE 1 ORDER BY `id` LIMIT ".($page-1)* $pagesize . ",".$pagesize;
		}else
		{
			$sql_kw = " AND `description` LIKE '%".trim($_GET['condition'])."%' ";
			$sql_page ="
				SELECT `id`, center_id,`value`, `description`, updatetime 
				FROM reset_hardware_info 
				WHERE 1 ".$sql_kw." ORDER BY `id` LIMIT ".($page-1)* $pagesize . ",".$pagesize;
		 		
		}
    
    //echo 'sql:'.$sql_page.'<BR>';
		$stmt  = $PDOLink->prepare($sql_page);
		$stmt->execute();
		$data = $stmt->fetchAll();		
		
	//	} 
		
	
	//$room_num  = $_GET['room_num'];
	// $mode      = $_GET['mode'];

	
	// if($door != '') {
		// $sql_kw .= " AND `id` = '{$door}' ";
	// }
	
	// if($floor) {
		// $sql_kw .= " AND `floor` = '{$floor}' ";
	// }
// 門禁初始
	// $sql = "SELECT count(*) as 'count' FROM door";
	// $rs  = $PDOLink->prepare($sql);
	// $rs->execute();
	// $tmp = $rs->fetch();
	// $issue_room = $tmp['count'];

	// $sql = "SELECT COUNT(*) as 'count' FROM `door` WHERE 1 ".$sql_kw;
	// $rs  = $PDOLink->query($sql);
	// $tmp = $rs->fetch();                      
	// $rownum = $tmp['count'];
/* if()
{
	
} */

    
  // 門禁初始SQL
	// $sql = "SELECT * FROM `door` WHERE 1 {$sql_kw} ORDER BY `name` LIMIT ".($page-1)* $pagesize . ",".$pagesize;
	// $rs  = $PDOLink->prepare($sql);
	// $rs->execute();
	// $data = $rs->fetchAll();
/* 
	$sql = "SELECT * FROM `door` ORDER BY `name`";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$door_arr  = $rs->fetchAll();
	
	foreach($door_arr as $v) {
		$dong_arr[$v['dong']]   = $v['dong'];
		$floor_arr[$v['floor']] = $v['floor'];
	} 
	
	$sql = "SELECT * FROM `custom_variables` WHERE `custom_catgory` = 'mode'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$mode_arr = $rs->fetchAll();
  
  	asort($dong_arr);
	asort($floor_arr); */
	
}else{
	echo "<script>alert('請使用管理者帳號登入');</script>";
	echo "<script>location.href = 'index.php';</script>";
	exit();
}

?>

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">硬體系統重啟</h1>

        <div class="row container-fluid mar-bot50">
        <?php if($_GET['success'] == 1){ ?>
            <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
            <strong>重啟完成！</strong>
            </div>
        <?php } elseif($_GET['error'] == 1){ ?> 
            <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
            <strong>重啟失敗！</strong>
            </div>	
        <?php } elseif($_GET['error'] == 2){ ?> 
            <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
            <strong>棟別不存在!重啟失敗！</strong>
            </div>	
        <?php } elseif($_GET['error'] == 3){ ?> 
            <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
            <strong>房號對應棟別不存在!重啟失敗！</strong>
            </div>	
        <?php } ?>
        </div>  


        <!-- Content Row -->
        <div class="row">

            <!-- 全部重啟&硬體現況 -->
            <!--
            <div class="col-xl-6 col-md-6 mb-4">
              <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="mb-1 font-weight-bold text-center">
                        <h5 class='text-green'>全部重啟</h5>
                        <hr>
                        <div class="mb-0">
                            <button type="button" onclick="all_reset()" class="btn  btn-h-auto text-white btnfont-30 font-weight-bold  btn-info col-12">
                              <i class="fab fa-osi"></i>
                              全部重啟
                            </button>
                        </div>
                      </div>
                      <br>

                      <div class="mb-1 font-weight-bold text-center">
                        <h5 class='text-green'>硬體現況</h5>
                        <hr>
                        <div class="alert alert-info mb-0">
                          <h4 class="my-6 p-3 font-weight-bold">共計<?php //echo $issue_room ?>個硬體NG</h4>    
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
            -->
            <!-- 全部重啟&硬體現況 END -->

            <!-- 棟別&樓層重啟 
            <div class="col-lg-6 col-sm-6 mb-4">
              <div class="card border-right-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="mb-1 font-weight-bold text-center">
                          <h5 class='text-green'>棟別&樓層重啟</h5>
                          <hr>
                      </div>

						          <form id='mform3' action="model/all_hw_reset.php" method="post" class='col-12'></form>
						          <form id='mform1' action="model/floor_hw_reset.php" method="post" class='col-12'>
                          <div class="mb-3">
                              <label for="exampleFormControlInput1" class="col col-form-label label-center">棟別</label>	
                                <select class="room_changes col form-control custom-select-lg"  size="1" name="dong" id="dong">
                                    <?php
    /*                                 foreach($dong_arr as $v) {
                                      echo "<option value='{$v['dong']}'>{$v['dong']}</option>";
                                    } */
                                    ?>
                                                  </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="exampleFormControlInput1" class="col col-form-label label-center">樓層</label>	
                                                  <select class="room_changes col form-control custom-select-lg"  size="1" name="floor" id="floor">
													<?php
/* 														foreach($floor_arr as $v) {
															$select  = ($floor == $v) ? 'selected' : '';
															echo "<option value='{$v}' {$select}>{$v}</option>";
														} */
													?>
												  </select>
											</div>
                          <br>
						  
                          <button type="submit" onclick="return confirm('確認重啟?')" class="btn  btn-h-auto text-white btnfont-30 font-weight-bold  btn-info col-12">
                              <i class="fab fa-osi"></i>
                              確認重啟
                          </button>
						          </form>

                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- 棟別&樓層重啟 END -->

        </div>
        <!-- Content Row -->


        <!--單個重啟--->
        <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header text-center text-white">
                  <h6 class="m-0 font-weight-bold text-center">重啟</h6>
                </div>
                <br>
                    <!--Search-->
                    <p class="text-lg text-center font-weight-bold NG-color">
                    【製作說明】查詢欄位：房號查詢。
                    </p>
                   
                    <form id='mform2' action="" method="get" class='col-12'>
                          <!-- 門禁初始欄位<div class="input-group mb-3">
                              <label for='exampleFormControlInput1' class=' col-sm-2 col-form-label label-right'>硬體名稱</label>
										          <input type='text' class='form-control' name='room_strings' placeholder='全部' value='<?php echo $room_num ?>'>
                              <select class="room_changes col-sm-8 form-control custom-select-lg"  size="1" name="door" >
                                <option value=''>全部</option>
                                <?php
                                  foreach($door_arr as $v) {
                                    $select = ($door == $v['id']) ? 'selected' : '';
                                    echo "<option value='{$v['id']}' {$select}>{$v['name']}</option>";
                                  }
                                ?>
                              </select>
                          </div> -->

                          <div class="input-group mb-3">
                              <label for='exampleFormControlInput1' class=' col-sm-2 col-form-label label-right'>房號</label>
							  <input type='text' class='col-sm-8 form-control custom-select-lg' name='condition' placeholder='全部' value=''>
                          </div>

                          <!-- <div class="input-group mb-3">
                              <label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right">樓層</label>	
                              <select class="room_changes col-sm-8 form-control custom-select-lg"  size="1" name="floor" id="floor" >
                                <option value=''>全部</option>
                                  <?php
                                    foreach($floor_arr as $v) {
                                      $select  = ($floor == $v) ? 'selected' : '';
                                      echo "<option value='{$v}' {$select}>{$v}</option>";
                                    }
                                  ?>
                                </select>
                          </div><br> -->
						  
						              <input type='hidden' name='serach' value='1'>
                          <button type='submit' class='btn  btnfont-30 btn-primary2 text-white col-lg-3 offset-lg-3'>查詢</button>
                          &nbsp;
                          <button type='button' class='btn  btnfont-30 btn-primary2 text-white col-lg-3' onclick='page_reset()'>重設</button>
                      </form>
                      <br>
                    <!--Search END-->



                <!--div class="card-body" style="display:<?php //echo ($serach != '') ? 'block' : 'none'?>"-->
                <div class="card-body" >
                      
                    <!--Table-->
                    <p class="text-lg font-weight-bold NG-color">【製作說明】</p>
                    <p class="text-lg font-weight-bold NG-color">
                      1.編號:前兩碼center_id、後兩碼重啟id，不足四碼自動補0<br>	            
                      =>EX.0116  01=center_id  16=重啟id<br>	
                    </p>
                   

                    <div class="table-responsive">
                                <table class="table  text-center font-weight-bold">
                                  <thead class="thead-green">
                                  <tr class="text-center">
                                    <th scope="col">重啟</th>
                                    <th scope="col">center_id</th>
                                    <th scope="col">重啟id</th>
                                    <th scope="col">編號(前兩碼center_id、後兩碼重啟id)</th>
                                    <th scope="col">對應房號</th>
                                  </tr>
                                  </thead>

                                  <tbody>
                                    <?php
                                      foreach($data as $w) {
                                        $center_id  = $w['center_id'];
                                        $value  = $w['value'];
                                        $room_num  = $w['description'];
                                        $center_num = sprintf('%02s', $center_id);
                                        $value_num = sprintf('%02s', $value);
                                        $id  = $w['id'];
                                    ?>
                                    <tr>
                                      <td scope="row">
                                        <button class="btn btn-orange btn-circle btn-lg2" title="重啟" onclick="room_reset('<?php echo $center_num.$value_num ;?>','<?php echo $id;?>')">
                                          <i class="fas fa-sync-alt font-weight-bold"></i>
                                        </button>
                                      </td>
                                      <td scope="row"><?php echo $center_id ?></td>
                                      <td scope="row"><?php echo $value?></td>
                                      <td scope="row"><?php echo $center_num.$value_num?></td>
                                      <td scope="row"><?php echo $room_num?></td>
                                    </tr>
                                    <?php	} ?>
                       
                                    </tbody>
                                </table>
                    </div>
                    <!--Table END--->
					
						<div class="row ">
							<div class="container-fluid">
								<div class="text-center" id="dataTable_paginate">
								<?php  
								if($rownum > $pagesize) {
									echo $pageurl;
									echo " ".$lang->line("index.current_page")." $page | ".$lang->line("index.a_few_pages")." $pagenum ".$lang->line("index.page")."";
								}
								?> 
								</div>
							</div>
						</div>
					
                </div>

              </div>
              
            </div>
        </div>
        <!--單間重啟 END--->


    </div>
    <!-- /.container-fluid -->

<script>
function room_reset(id,reset_id) {
	if(confirm("確認重啟?")) {
		location.replace('model/hw_reset.php?id=' + id + "&reset_id=" + reset_id);
	}
	return;
}

function all_reset() {
	if(confirm("確認重啟?")) {
		$('#mform3').submit();
	}
	return;
}

function page_reset() {
	location.replace('system-reset.php');
}
</script>

<?php
    include('includes/footer.php');
?>