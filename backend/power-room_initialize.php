<?php
    include('includes/header.php');
    include('includes/nav.php');
	  include('includes/scripts.php');

    $pagesize = 10;

    $dong_arr  = array();
    $floor_arr = array(); 
    
    $sql_kw    = '';
    $room_num  = $_GET['room_num'];
    $mode      = $_GET['mode'];
    $serach    = $_GET['serach'];
    
    if($room_num != '') {
      $sql_kw .= " AND `name` = '{$room_num}' ";
    }
    
    if($mode != '') {
      $sql_kw .= " AND `mode` = '{$mode}' ";
    }	

    $sql = "SELECT count(*) as 'count' FROM room";
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
    $tmp = $rs->fetch();
    $issue_room = $tmp['count'];

    $sql = "SELECT COUNT(*) as 'count' FROM `room` WHERE 1 ".$sql_kw;
    $rs  = $PDOLink->query($sql);
    $tmp = $rs->fetch();                      
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


    $sql = "SELECT * FROM `room` WHERE 1 {$sql_kw} ORDER BY id LIMIT ".($page-1)* $pagesize . ",".$pagesize;
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
      $data = $rs->fetchAll();
  
    $sql = "SELECT * FROM `custom_variables` WHERE `custom_catgory` = 'mode'";
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
    $mode_arr = $rs->fetchAll(); 
    
    # 棟別
    $sql = "SELECT DISTINCT a.dong, b.dong_name FROM `room` a INNER JOIN dongname b ON b.dong=a.dong";
    $dong_arr = func::excSQL($sql, $PDOLink, true);
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">宿舍房間初始化</h1>

        <div class="row container-fluid mar-bot50">
        <?php if($_GET['success'] == 1){ ?>
            <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
            <strong>初始化完成！</strong>
            </div>
        <?php } elseif($_GET['error'] == 1){ ?> 
            <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
            <strong>初始化失敗！</strong>
            </div>	
        <?php } ?>
        </div>  

        <!--單間初始化--->
        <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header text-center text-white">
                  <h6 class="m-0 font-weight-bold text-center">單間初始化</h6>
                </div>
                <br>
                    <!--Search-->
                    <!--
                    <p class="text-lg text-center font-weight-bold NG-color">
                      查詢欄位：房號、狀態 擇一選擇即可查詢。
                    </p>
                    -->
                    <form id='mform2' action="" method="get" class='col-12'>
                          <div class="input-group mb-3 input-group-lg">
                              <label for='exampleFormControlInput1' class=' col-sm-2 col-form-label label-right'>房號</label>
                              <input  type='text' class='form-control  col-sm-8' name='room_num' placeholder='全部' value='<?php echo $room_num ?>'>
                          </div>

                          <div class="input-group mb-3">
                              <label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right">狀態</label>	
                                <select class="room_changes col-sm-8 form-control custom-select-lg"  size="1" name="mode"  >
                                <option value=''>全部</option>
								<?php
									foreach($mode_arr as $v) {
										$opt_key = $v['custom_id'];
										$opt_val = $v['custom_var'];
										$select  = ($opt_key == $mode) ? 'selected' : '';
										echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
									}
								?>
                                </select>
                          </div><br>
						  
						  <input type='hidden' name='serach' value='1'>
                          <button type='submit' class='btn  btnfont-30 btn-primary2 text-white col-lg-3 offset-lg-3'>查詢</button>
                          &nbsp;
                          <button type='button' class='btn  btnfont-30 btn-primary2 text-white col-lg-3' onclick='page_reset()'>重設</button>
                          <!--
                          <button type='button' onclick='export_list()' class='btn  btn-loginfont btn-primary2  col-4 offset-4'><?php //echo $lang->line("index.confirm_query") ?></button>
                          <button type='button' onclick='export_list()' class='btn  btn-loginfont btn-primary2  col-4 offset-4'><?php //echo $lang->line("index.confirm_query") ?></button>
                          -->
                      </form>
                      <br>
                    <!--Search END-->



                <div class="card-body" style="display:<?php echo ($serach != '') ? 'block' : 'none'?>">
                      
                    <!--Table-->
                    
                    <!--
                    <p class="text-lg text-center font-weight-bold NG-color">叮嚀：預查表格資料帶出全部資料。資料比照北護以房號排序</p>
                    <p class="text-lg text-center font-weight-bold NG-color">
                      欄位:卡號、姓名、開電前度數、開電後度數、開電前金額、開電後金額、扣款金額、狀態要呈現四筆                      
                    </p>
                    -->

                    <div class="table-responsive">
                                <table class="table  text-center font-weight-bold">
                                  <thead class="thead-green">
                                  <tr class="text-center">
                                    <th scope="col">初始化</th>
                                    <th scope="col">房號</th>
                                    <th scope="col">費率</th>
                                    <th scope="col">當前度數</th>
                                    <th scope="col">類型</th>
                                    <th scope="col">狀態</th>
                                    <th scope="col">卡號</th>
                                    <th scope="col">開電前度數</th>
                                    <th scope="col">開電後度數</th>
                                    <th scope="col">開電前金額</th>
                                    <th scope="col">開電後金額</th>
                                    <th scope="col">扣款金額</th>
                                    <th scope="col">姓名</th>
                                  </tr>
                                  </thead>

                                  <tbody>
                                    <!--沒用電時--->
									<?php
									foreach($data as $w) {
										
										$room_id      = $w['id'];
										$room_num     = $w['name'];
										$price_degree = $w['price_degree'];
										$amount       = $w['amount'];
										$title = $w['Title'];
										// $status_str   = $w['mode'] == 2 ? "power-on" : "power-off";
										
										$column_on    = "<b class='b-label col'>%s</b><br>";
										$column_off   = "<b class='b-label bg-gray-500 col'>%s</b><br>";
										
										$sql = "
											SELECT r.*, m.cname, m.id_card
											FROM `room_electric_situation` r 
											LEFT JOIN member m ON m.id = r.member_id
											WHERE r.room_id = ".$room_id;
										$rs  = $PDOLink->prepare($sql);
										$rs->execute();
										$room_elec = $rs->fetchAll();
										
										# 單間有無用電中
										$sql = "SELECT max(powerstaus) AS powerst FROM room_electric_situation  WHERE room_id ={$room_id} LIMIT 1 ";
										$status_ = func::excSQL($sql, $PDOLink, false);
										$status_str = ($status_['powerst'] == 1)?  "fas fa-circle power-on" : "fas fa-circle power-off";										
									?>
                                    <tr>
                                      <td scope="row">
                                      <button class="btn btn-info btn-circle btn-lg2" title="初始化" onclick="room_initialize('<?php echo $room_id ?>')">
                                        <i class="fab fa-osi font-weight-bold"></i>
                                      </button>

                                      </td>
                                      
                                      <td scope="row"><?php echo $room_num ?></td>
                                      <td scope="row"><?php echo $price_degree ?></td>
                                      <td scope="row"><?php echo $amount ?></td>
                                      <td scope="row"><?php echo $title ?></td>
                                      <td scope="row"><i class="<?php echo $status_str; ?>"></i></td>

                                      <td scope="row">
                                        <div class="b-align">
										<?php											
											foreach($room_elec as $v) {
												echo ($v['powerstaus'] == 1) ? sprintf($column_on, $v['id_card']) : sprintf($column_off, $v['id_card']);
											}
										?>
                                        </div>                                      
                                      </td>
                                      
                                      <td scope="row">
                                        <div class="b-align">
										<?php foreach($room_elec as $v) {
												echo ($v['powerstaus'] == 1) ? sprintf($column_on, $v['start_amonut']) : sprintf($column_off, $v['start_amonut']);
											}
										?>
                                        </div>                                      
                                      </td>
                                      <td scope="row">
                                        <div class="b-align">
										<?php foreach($room_elec as $v) {
												echo ($v['powerstaus'] == 1) ? sprintf($column_on, $v['now_amount']) : sprintf($column_off, $v['now_amount']);
											}
										?>
                                        </div> 
                                      </td>

                                      <td scope="row">
                                        <div class="b-align">
										<?php foreach($room_elec as $v) {
												echo ($v['powerstaus'] == 1) ? sprintf($column_on, $v['start_balance']) : sprintf($column_off, $v['start_balance']);
											}
										?>
                                        </div>                                         
                                      </td>

                                      <td scope="row">
                                        <div class="b-align">
										<?php foreach($room_elec as $v) {
												echo ($v['powerstaus'] == 1) ? sprintf($column_on, $v['now_balance']) : sprintf($column_off, $v['now_balance']);
											}
										?>
                                        </div> 
                                      </td>
                                      
                                      <td scope="row">
                                        <div class="b-align">
										<?php foreach($room_elec as $v) {
												$balance = $v['now_balance'] - $v['start_balance'];
												echo ($v['powerstaus'] == 1) ? sprintf($column_on, $balance) : sprintf($column_off, $balance);
											}
										?>
                                        </div> 
                                      </td>
                                      
                                      <td scope="row">
                                        <div class="b-align">
										<?php foreach($room_elec as $v) {
												echo ($v['powerstaus'] == 1) ? sprintf($column_on, $v['cname']) : sprintf($column_off, $v['cname']);
											}
										?>
                                        </div>
                                      </td>
                                    </tr>
									<?php	
									}
									?>
                                    <!--沒用電時 END--->
                       
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
        <!--單間初始化 END--->

        <!-- Content Row -->
        <div class="row">
		
            <!-- 棟別&樓層初始化 -->
            <div class="col-lg-6 col-sm-6 mb-4">
              <div class="card border-right-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="mb-1 font-weight-bold text-center">
                          <h5 class='text-green'>棟別&樓層初始化</h5>
                          <hr>
                      </div>

						          <form id='mform3' action="model/all_initialize.php" method="post" class='col-12'></form>
						          <form id='mform1' action="model/floor_initialize.php" method="post" class='col-12'>
                          <div class="mb-3">
                              <label for="exampleFormControlInput1" class="col col-form-label label-center">棟別</label>	
                                <select class="room_changes col form-control custom-select-lg"  size="1" name="dong" id="dong" required>
								<option value=''>請選擇</option>
								  <?php
                                    foreach($dong_arr as $v) {
                                      echo "<option value='{$v['dong']}'>{$v['dong_name']}</option>";
                                    }
                                    ?>
                                                  </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="exampleFormControlInput1" class="col col-form-label label-center">樓層</label>	
                                                  <select class="room_changes col form-control custom-select-lg"  size="1" name="floor" id="floor" required></select>
											</div>
                          <br>
						  
                          <button type="submit" onclick="return confirm('確認初始化?')" class="btn  btn-h-auto text-white btnfont-30 font-weight-bold  btn-info col-12">
                              <i class="fab fa-osi"></i>
                              確認初始化
                          </button>
						          </form>

                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- 棟別&樓層初始化 END -->
			
            <!-- 全部初始化&房間現況 -->
            <div class="col-xl-6 col-md-6 mb-4">
              <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="mb-1 font-weight-bold text-center">
                        <h5 class='text-green'>全部初始化</h5>
                        <hr>
                        <div class="mb-0">
                            <button type="button" onclick="all_initialize()" class="btn  btn-h-auto text-white btnfont-30 font-weight-bold  btn-info col-12">
                              <i class="fab fa-osi"></i>
                              全部初始化
                            </button>
                        </div>
                      </div>
                      <br>

                      <div class="mb-1 font-weight-bold text-center">
                        <h5 class='text-green'>房間現況</h5>
                        <hr>
                        <div class="alert alert-info mb-0">
                          <h4 class="my-6 p-3 font-weight-bold"><?php echo $issue_room ?>間資料已上傳完畢</h4>    
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- 全部初始化&房間現況 END -->

        </div>
        <!-- Content Row -->

    </div>
    <!-- /.container-fluid -->

<script>

$(document).ready(function() {
	
	combine_data();
	
	$('#dong').change(function() {
		combine_data();
	});
});

function room_initialize(id) {
	if(confirm("確認初始化?")) {
		location.replace('model/room_initialize.php?id=' + id);
	}
	return;
}

function all_initialize() {
	// if(confirm("確認初始化?")) {
	// 	$('#mform3').submit();
	// }
  var chkText=prompt("確認全部初始化?\n請輸入「Yes」\n大小寫皆須相符！");
  if(chkText == 'Yes') {
		$('#mform3').submit();
  }else if(chkText == null){
    return;
  }else{
    alert("key錯了，初始化失敗請重新輸入！");
    return;
  }
}

function page_reset() {
	location.replace('power-room_initialize.php');
}

function combine_data() {
	
	if($('#dong').val() != '') {
		$.ajax({
			url: "model/ajax_floor_init_list.php",
			data: { dong: $('#dong').val()},
			type: 'post',
			success: function(data) {
				$('#floor').html(data);
			}
		});
	} else {
		$('#floor').html("<option value=''>請選擇</option>");
	}
}
</script>

<?php
    include('includes/footer.php');
?>