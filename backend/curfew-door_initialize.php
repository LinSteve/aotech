<?php
    include('includes/header.php');
    include('includes/nav.php');
    include('includes/scripts.php');

    //$dong_arr  = array();
    $floor_arr = array();	
    
    $where    = '';
    $param = array();
    $room_num  = $_GET['room_num'];
    $mode      = $_GET['mode'];
    $serach    = $_GET['serach'];
    
    if($room_num != '') {
      $where .= " AND `name` = :name ";
      $param[':name'] = $room_num;
    }
    
    if($mode != '') {
      $where .= " AND `mode` = :mode ";
      $param[':mode'] = $mode;
    }	
  
    $sql = "SELECT * FROM `room` WHERE 1 {$where} ORDER BY id " ; // LIMIT ".($page-1)* $pagesize . ",".$pagesize; 
    $data = func::excSQLwithParam('select', $sql, $param, true, $PDOLink);	
    
    $sql = "SELECT a.dong,b.dong_name, a.floor FROM `room` a LEFT JOIN dongname b ON b.dong=a.dong GROUP BY dong";
    $room_arr = func::excSQL($sql, $PDOLink, true);
       
    $sql = "SELECT * FROM `custom_variables` WHERE `custom_catgory` = 'mode'";
    $mode_arr = func::excSQL($sql, $PDOLink, true);
    
    //asort($dong_arr);
?>
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">卡機重啟</h1>
        <div class="row container-fluid mar-bot50" id = 'resultMsg'>
          <?php if($_GET['success'] == 1){ ?>
              <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
              <strong>開啟完成！</strong>
              </div>
          <?php } elseif($_GET['error'] == 1){ ?> 
              <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
              <strong>開啟失敗！</strong>
              </div>	
          <?php } elseif($_GET['error'] == 2){ ?> 
              <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
              <strong>請選擇棟別！</strong>
              </div>				  
          <?php } ?>
        </div>   
        <!-- Content Row -->
        <div class="row justify-content-center"> 
            <!-- 全部房間開啟 --> 
            <div class="col-xl-6 col-md-6 mb-4" style="display:none;">
              <div class="card border-left-orange shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="mb-1 font-weight-bold text-center">
                        <h5 class='text-green'>全部房門開啟設定</h5>
                        <hr>
                        <div class="mb-0">
                            <button type="button" onclick="all_initialize()" class="btn  btn-h-auto text-white btnfont-30 font-weight-bold  btn-orange col-12">
                              <i class="fas fa-door-open"></i>
                              全部開啟
                            </button>
                        </div>
                      </div>
                      <br> 
                      <div class="mb-1 font-weight-bold text-center">
                        <h5 class='text-green'>房間現況</h5>
                        <hr>
                        <div class="alert alert-info mb-0">
                          <h4 class="my-6 p-3 font-weight-bold"><?php //echo $issue_room;  ?>間資料已上傳完畢</h4>    
                        </div>
                      </div> 
                    </div>
                  </div>
                </div>
              </div>
            </div> 
            <!-- 全部房間開啟 END -->

            <!-- 整棟別&樓層門開啟 -->
            <div class="col-xl-6 col-md-6 mb-4 order-last">
              <div class="card border-right-orange border-left-orange shadow py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="mb-1 font-weight-bold text-center">
                          <h5 class='text-green'>棟別&樓層卡機重啟</h5>
                          <hr>
                      </div>  
                      <form id='mform1'  method='get'   class='col-12' action='model/door_open.php'>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="col col-form-label label-center">棟別</label>	
                              <select class="room_changes col form-control custom-select-lg"  size="1" name="dong" id="dong" required>
                                  <option value=''>請選擇</option>
                                  <?php
                                      foreach($room_arr as $v) {
                                        echo 'dong_name:'.$v['dong_name'].'<BR>';
                                        echo "<option value='{$v['dong']}'>{$v['dong_name']}</option>";
                                      }
                                  ?>
                              </select>
                        </div>
                        <div class="mb-3">
                          <label for="exampleFormControlInput1" class="col col-form-label label-center">樓層</label>	
                          <select class="room_changes col form-control custom-select-lg"  size="1" name="floor" id="floor" required>
                              <option value=''>全部</option>
                                        <?php
                                         foreach($floor_arr as $v) {
                                           echo "<option value='{$v['floor']}'>{$v['floor']}</option>";
                                         }
                                        ?>
                          </select>
                        </div>                    
                        <br> 
                        <!--a  onclick="floor_open()" class="btn  btn-h-auto text-white btnfont-30 font-weight-bold  btn-orange col-12"> 
                            確認開啟
                        </a>-->
							<input type='hidden' name = 'type' value='floor'>
						   <button type="submit"   onclick="return confirm('卡機將重啟，是否確認重啟?')" class="btn  btn-h-auto text-white btnfont-30 font-weight-bold  btn-orange col-12"> 
                            確認重啟
                        </button>
                      </form> 
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- 整棟別&樓層門開啟 END --> 

            <!--單間房門開啟--->
                <div class="col-xl-6 col-md-6 ">
                  <div class="card mb-4">
                    <div class="card-header text-center text-white">
                      <h6 class="m-0 font-weight-bold text-center">單間房間卡機重啟</h6>
                    </div>
                    <br>
                        <!--Search-->
                        <form id='form1' action="" method="get" class='col-12'>
                              <div class="input-group mb-3 input-group-lg">
                                  <label for='exampleFormControlInput1' class=' col-sm-2 col-form-label label-right'>房號</label>
                                  <input  type='text' class='form-control  col-sm-8' id='room_num' id='room_num' name='room_num' placeholder='全部' value='<?php echo $room_num ?>'>
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
                              <a onclick='sch()' class='btn  btnfont-30 btn-primary2 text-white col-lg-3 offset-lg-3'>查詢</a>
                              &nbsp;
                              <button type='button' class='btn  btnfont-30 btn-primary2 text-white col-lg-3' onclick='page_reset()'>重設</button> 
                        </form>
                        <br>
                        <!--Search END-->  
                    <div class="card-body" style="display:<?php echo ($serach != '') ? 'block' : 'none'?>">  
                        <div class="table-responsive">
                            <table class="table  text-center font-weight-bold"> 
                              <tbody> 
                                <?php
                                if(trim($_GET['room_num']) && $data)
                                {									
                                  foreach($data as $w) 
                                  {	
                                    $room_id = $w['id'];
                                    $room_num  = strtoupper($w['name']);  
                                    echo "<div id ='result' class='col-lg-8 offset-lg-2 card card-green mb-4 py-4 border-0'>
                                    <h1 class='text-center'>房號：{$room_num}</h1>
                                    <button class='btn btn-orange btn-h-auto text-white btnfont-30 col-md-6  offset-md-3' onclick='door_initialize($room_id)'>
                                      卡機重啟
                                    </button>
                                    </div>	"; 
                                  }
                                }
                                ?> 
                              </tbody>
                            </table>
                        </div>
                        <!--Table END---> 
                        <div class="row ">
                          <div class="container-fluid">
                            <div class="text-center" id="dataTable_paginate"> 
                            </div>
                          </div>
                        </div> 
                    </div> 
                  </div> 
                </div>
            <!--單間房門開啟 END--->
        </div>
        <!-- Content Row --> 
    </div>
    <!-- /.container-fluid -->
<script>

$(document).ready(function() {
 
	$('#dong').change(function() {
		combine_data();
		$('#resultMsg').html('');
	});
	$('#floor').change(function() { 
		$('#resultMsg').html('');
	});
});
 
function door_initialize(id) //單間開
{
	if(confirm("卡機將重啟，是否確認重啟?")) {
		location.replace('model/door_open.php?id=' + id+'&type=single');
	}else{
		return false;
	}
}
 
function sch()
{	
	if($('#room_num').val().trim() == '')
	{
		alert('請輸入房號 ! ');
		$('#result').hide();
		return false;
	} else{
		$('#form1').submit();
	}
}
function page_reset() {
	location.replace('curfew-door_initialize.php');
}

function combine_data() {
	
	if($('#dong').val() != '') {
		$.ajax({
			url: "model/combine_floor_all.php",
			data: { dong: $('#dong').val(), },
			type: 'post',
			success: function(data) {
				$('#floor').html(data);
			}
		});
	} else {
		$('#floor').html('');
		$('#floor').html(' <option>全部</option>');
	}
}
</script>

<?php  
    include('includes/footer.php');
?>