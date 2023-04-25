<?php
include('includes/header.php');
include('includes/nav.php');
include('includes/scripts.php');

//$lang = new Language();
//$lang->load("index");

$room_id = '';
$price = '';
$mode = '';
$price_220 = '';
$mode_220 = '';
$room_num = $_GET["room_numbers_kw"];

$price_all_mode = $_GET["price_all_mode"];
$price_all_degree = $_GET["price_all_degree"];

$price_floor_mode = $_GET["price_floor_mode"];
$price_floor_degree = $_GET["price_floor_degree"];

if ($room_num) {
  $sql = "SELECT * FROM `room` WHERE `name` = '{$room_num}'";
  $rs = $PDOLink->prepare($sql);
  $rs->execute();
  $tmp = $rs->fetch();

  $room_id = $tmp['id'];
  $mode = $tmp['mode'];
  $price = $tmp['price_degree'];
  $mode_220 = $tmp['mode_220'];
  $price_220 = $tmp['price_degree_220'];
}

if (!isset($price_all)) {
  $sql = "SELECT * FROM `room` LIMIT 0, 1";
  $rs = $PDOLink->prepare($sql);
  $rs->execute();
  $tmp = $rs->fetch();

  $price_all_degree = $tmp['price_degree'];
  $price_all_mode = $tmp['mode'];

  $price_floor_degree = $price_all_degree;
  $price_floor_mode = $price_all_mode;
}

$sql = "SELECT * FROM `custom_variables` WHERE `custom_catgory` = 'mode'";
$rs = $PDOLink->prepare($sql);
$rs->execute();
$mode_arr = $rs->fetchAll();

$sql = "SELECT dong, floor FROM `room` GROUP BY dong, floor";
$rs = $PDOLink->prepare($sql);
$rs->execute();
$room_arr = $rs->fetchAll();
// $dong_arr  = array();
$floor_arr = array();

foreach ($room_arr as $v) {
  $dong_arr[$v['dong']] = $v['dong'];
  $floor_arr[$v['floor']] = $v['floor'];
}

ksort($floor_arr);
// ksort($dong_arr);

# 棟別
$sql = "
	SELECT DISTINCT a.dong, b.dong_name FROM `room` a
	INNER JOIN dongname b ON a.dong=b.dong;
	";
$dong_arr = func::excSQL($sql, $PDOLink, true);

?>
<!-- 更新&錯誤資訊 放這 -->
<div class="row container-fluid mar-bot50 mar-center2">
  <?php if ($_GET['success'] == 1) { ?>
    <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
      <strong>
		<?php echo $lang->line("index.success_room_settings"); ?>!!
      </strong>
    </div>
  <?php } elseif ($_GET['success'] == 2) { ?>
    <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
      <strong>
        <?php echo $lang->line("index.success_room_settings_all"); ?>!!
      </strong>
    </div>
  <?php } elseif ($_GET['error'] == 1) { ?>
    <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
      <strong>Error 資料輸入錯誤或不存在</strong>
    </div>
  <?php } elseif ($_GET['error'] == 2) { ?>
    <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
      <strong>請選擇棟別或樓層</strong>
    </div>
  <?php } elseif ($_GET['error'] == 3) { ?>
    <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
      <strong>請至少設定一項值</strong>
    </div>
  <?php } ?>
</div>

<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Page Heading -->
  <h1 class="mb-2 font-weight-bold">費率 & 收費模式</h1>
  <!-- Content Row -->
  <div class="row">
    <!-- 個別房間設定 -->
    <div class="col-lg-4">
      <div class="card shadow mb-4">

        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-center">個別房間設定</h6>
        </div>
        <div class="card-body ">
          <div class="form-group">
            <form action="" method="get">
              <label for="exampleInputEmail1" class="label-center col btn-marbot20">輸入房號</label>
              <input type="search" name="room_numbers_kw" value="<?php echo $room_num ?>"
                class=" form-control  col-8 offset-2" placeholder="Ex. T201">
              <!--<input type="search" name="room_numbers_kw" value="<?php// echo $room_num ?>" class=" form-control  col-8 offset-2" placeholder="Ex. A101">-->
			  	<button class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-6 offset-3">查詢</button>
			
            <!--  <button type='submit'
                class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-6 offset-3">查詢</button>-->
            </form>
          </div>

          <?php
          if (	$room_id != '' ) {
            ?>
            <div class="form-group  btn-martop30">
              <form name="myForm2" id="myForm2" action="model/upd_room_rate_mode.php" method="post">
                <label for="exampleInputPassword1" class="label-center col">110V用電費率</label>
                <input step="0.1" min="0" max="25.5" type="number" required="required" class="form-control col-8 offset-2"
                  name="price_degree" id="price_degree" value="<?php echo $price; ?>">
                <label for="exampleInputPassword1" class="label-center col  ">110V收費設定</label>
                <select class='form-control col-8 offset-2 input-lg2' size='1' name='mode' id='mode' >
                  <?php
                  foreach ($mode_arr as $v) {

                    $opt_key = $v['custom_id'];
                    $opt_val = $v['custom_var'];
                    $select = ($opt_key == $mode) ? 'selected' : '';

                    echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
                  }
                  ?>
                </select>
                <!-- 新增220V -->
                <label for="exampleInputPassword1" class="label-center col ">220V用電費率</label>
                <input step="0.1" min="0" max="25.5" type="number" required="required" class="form-control col-8 offset-2"
                  name="price_degree_220" id="price_degree_220" value="<?php echo $price_220; ?>">

                <label for="exampleInputPassword1" class="label-center col ">220V收費設定</label>
                <select class='form-control col-8 offset-2 input-lg2' size='1' name='mode_220' id='mode_220' >
                  <?php
                  foreach ($mode_arr as $v) {

                    $opt_key = $v['custom_id'];
                    $opt_val = $v['custom_var'];
                    $select = ($opt_key == $mode_220) ? 'selected' : '';

                    echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
                  }
                  ?>
                </select>
                <input type='hidden' name='room_id' id='room_id' value='<?php echo $room_id ?>'>
                <input type='hidden' name='room_num' id='room_num' value='<?php echo $room_num ?>'>
				<p id="result"></p> 
			<!--	<input type="submit" name="button" id="submitExample" value="確認更新" class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-sm-6 offset-sm-3">-->
                <button type="submit" onclick="return confirm('確認更新?')"
                  class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-sm-6 offset-sm-3">確認更新</button>
              </form>
            </div>
            <?php
          }
          ?>

          <!--綁後端後的code
                    <div class="form-group  btn-martop30">
                        <?php
                        if ($price != '') {
                          ?>
                            <form id="myForm2" action="model/rate_upd.php" method="post">
                              <input type='hidden' name='room_id'  value='<?php echo $room_id ?>'>
                              <input type='hidden' name='room_num' value='<?php echo $room_num ?>'>
                              <label for="exampleInputPassword1"  class="label-center col btn-marbot20">用電度數</label>
                              <input step="0.1" min="0" max="25.5" type="number" required="required" class="form-control col-8 offset-2" name="price_elec_degree" value="<?php //echo $price; ?>">
                              <button type="submit"  onclick="return confirm('確認更新?')"  class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-6 offset-3">確認更新</button> 
                            </form>					
                        <?php
                        }
                        ?>
                    </div>
                    -->

        </div>

      </div>
    </div>

    <!-- 整層樓房間設定 -->
    <div class="col-lg-4">
      <div class="card shadow mb-4">

        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-center">整層樓房間設定</h6>
        </div>

        <div class="card-body ">

          <div class="form-group">
            <form id="myForm3" action="model/upd_floor_rate_mode.php" method="post">
              <label for="exampleInputPassword1" class="label-center col btn-marbot20">棟別</label>
              <select class='form-control col-8 offset-2 input-lg2' size='1' name='dong' id='dong' required>
                <option value=''>請選擇</option>
                <?php
                foreach ($dong_arr as $v) {
                  echo "<option value='{$v['dong']}'>{$v['dong_name']}</option>";
                }
                ?>
              </select>
              <label for="exampleInputPassword1" class="label-center col btn-marbot20 btn-martop30">樓層</label>
              <select class='form-control col-8 offset-2 input-lg2' size='1' name='floor' id='floor' required>
                <?php
                // foreach($floor_arr as $k => $v) {
                
                // $select  = ($k == $floor) ? 'selected' : '';
                
                // echo "<option value='{$k}' {$select}>{$v}</option>";
                // }
                ?>
              </select>
              <label for="exampleInputPassword1" class="label-center col label-top">110V用電費率</label>
              <input step="0.1" min="0" max="25.5" type="number" class="form-control col-8 offset-2"
                name="price_floor_degree" value="" placeholder="Ex:4.5">

              <label for="exampleInputPassword1" class="label-center col label-top">110V收費設定</label>
              <select class='form-control col-8 offset-2 input-lg2' size='1' name='price_floor_mode'>
                <option value=''>請選擇</optoin>
                  <?php
                  foreach ($mode_arr as $v) {

                    $opt_key = $v['custom_id'];
                    $opt_val = $v['custom_var'];
                    // $select  = ($opt_key == $price_floor_mode) ? 'selected' : '';
                  
                    echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
                  }
                  ?>
              </select>
              <!-- 新增220V -->
              <label for="exampleInputPassword1" class="label-center col label-top">220V用電費率</label>
              <input step="0.1" min="0" max="25.5" type="number" class="form-control col-8 offset-2"
                name="price_floor_degree_220" value="" placeholder="Ex:4.5">

              <label for="exampleInputPassword1" class="label-center col label-top">220V收費設定</label>
              <select class='form-control col-8 offset-2 input-lg2' size='1' name='price_floor_mode_220'>
                <option value=''>請選擇</optoin>
                  <?php
                  foreach ($mode_arr as $v) {

                    $opt_key = $v['custom_id'];
                    $opt_val = $v['custom_var'];
                    // $select  = ($opt_key == $price_floor_mode) ? 'selected' : '';
                  
                    echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
                  }
                  ?>
              </select>
              <button type="submit" onclick="return confirm('確認更新?')"
                class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-sm-6 offset-sm-3">確認更新</button>
            </form>
          </div>

        </div>
      </div>
    </div>
    <!-- 全部房間設定 -->
    <div class="col-lg-4">
      <div class="card shadow mb-4">

        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-center">全部房間設定</h6>
        </div>

        <div class="card-body ">

          <div class="form-group">
            <form id="myForm4" action="model/upd_all_rate_mode.php" method="post">
              <label for="exampleInputPassword1" class="label-center col label-top">110V用電費率</label>
              <input step="0.1" min="0" max="25.5" type="number" class="form-control col-8 offset-2"
                name="price_all_degree" value="" placeholder="Ex:4.5">

              <label for="exampleInputPassword1" class="label-center col label-top">110V收費設定</label>
              <select class='form-control col-8 offset-2 input-lg2' size='1' name='price_all_mode'>
                <option value=''>請選擇</option>
                <?php
                foreach ($mode_arr as $v) {
                  $opt_key = $v['custom_id'];
                  $opt_val = $v['custom_var'];
                  // $select  = ($opt_key == $price_all_mode) ? 'selected' : ''; 
                  echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
                }
                ?>
              </select>
              <!-- 新增220V -->
              <label for="exampleInputPassword1" class="label-center col label-top">220V用電費率</label>
              <input step="0.1" min="0" max="25.5" type="number" class="form-control col-8 offset-2"
                name="price_all_degree_220" value="" placeholder="Ex:4.5">

              <label for="exampleInputPassword1" class="label-center col label-top">220V收費設定</label>
              <select class='form-control col-8 offset-2 input-lg2' size='1' name='price_all_mode_220'>
                <option value=''>請選擇</option>
                <?php
                foreach ($mode_arr as $v) {
                  $opt_key = $v['custom_id'];
                  $opt_val = $v['custom_var'];
                  // $select  = ($opt_key == $price_all_mode) ? 'selected' : ''; 
                  echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
                }
                ?>
              </select>
              <button type="submit" onclick="return confirm('確認更新?')"
                class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold  btn-primary2 col-sm-6 offset-sm-3">確認更新</button>
            </form>
          </div>

        </div>

      </div>
    </div>
  </div>
  <!-- Content Row -->


</div>
<!-- /.container-fluid -->

<script>

  $(document).ready(function () {

    combine_data();

    $('#dong').change(function () {
      combine_data();
    });
  });

  function combine_data() {

    if ($('#dong').val() != '') {
      $.ajax({
        url: "model/ajax_floor_init_list.php",
        data: { dong: $('#dong').val() },
        type: 'post',
        success: function (data) {
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