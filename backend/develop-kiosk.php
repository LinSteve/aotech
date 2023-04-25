<?php
    include('includes/header.php');
    include('includes/nav.php');
	include('includes/scripts.php');
	
	$pagesize = 10;
	
	$sql = "SELECT count(*) as 'count' FROM system_setting WHERE title = '後台' ";
	$sql = "SELECT count(*) as 'count' FROM system_setting WHERE 1 ";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
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
		$pageurl .= "<a href='?page=1'>".$lang->line("index.home").
					"</a> | <a href='?page={$prepage}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}'>".$lang->line("index.next_page").
					"</a> | <a href='?page={$pagenum}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT * FROM system_setting WHERE title = '後台' 
			ORDER BY id DESC LIMIT ".($page-1)* $pagesize . ",".$pagesize;
	$sql = "SELECT * FROM system_setting WHERE 1
			ORDER BY id DESC LIMIT ".($page-1)* $pagesize . ",".$pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data_arr = $rs->fetchAll();
	
	$sql = "SELECT * FROM `host`";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$host_arr = $rs->fetchAll();
	$host_map = array();
	
	foreach($host_arr as $v) {
		$host_map[$v['name']] = $v['dong'];
	}
	
	$sql = "SELECT * FROM var_list WHERE var_type = '工程模式'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$cmd_arr = $rs->fetchAll();
?>

<!-- Begin Page Content -->
<div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">工程模式設定</h1>
        <!--
        <p class="text-lg text-center font-weight-bold NG-color">
          可參考北護的「工程模式設定」處理。後端會帶出哪些命令？<br>
          哪些命令會跳哪些題項具體要看弘光資料庫有哪些命令(依北護後台的題項顯示：輸入數值、選擇棟別、選擇電腦)
        </p>
        -->
		<!-- SEARCH 工程模式設定-->
		<div class='col-12'>
				<form method="get">
						<div class='panel-body'>

								<div class='form-group row mx-0'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>選擇命令</label>
									<div class='col-sm-8 input-group-lg'> 
                    <select class="form-control" name="cmd" >  
                        <option value="">請選擇</option> 
						<?php
							foreach($cmd_arr as $v) {
								$opt_key = $v['sn'];
								$opt_val = $v['var_name'];
								$select  = ($opt_key == $mode) ? 'selected' : '';
								
								echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
							}
						?>
                    </select>
									</div>
                </div>
                <br>

								<div class='col-sm-8 offset-lg-2 input-group-lg'> 
                    <label for='exampleFormControlInput1' class='label-left'>
                      輸入數值：只有「手動更新批次號」及「下載指定FTP資料」會需要填入任一數值，其他命令可跳過此項
                    </label>
                    <input class="form-control" type="number" name="number_value" value="1">
								</div>
                <br>

                <div class='col-sm-8 offset-lg-2 input-group-lg'>
									<label for='exampleFormControlInput1' class='label-left'>選擇棟別</label>
                    <select class="form-control" name="dong" id="">  
                        <option value="請選擇">請選擇</option> 
						<?php
							foreach($host_arr as $v) {
								$opt_key = $v['dong'];
								$opt_val = $v['dong'];
								$select  = ($opt_key == $mode) ? 'selected' : '';								
								echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
							}
						?>
                    </select>
                </div> 
                <br>

                <div class='col-sm-8 offset-lg-2 input-group-lg'>
                  <label for='exampleFormControlInput1' class='label-left'>選擇電腦</label>
                    <select class="form-control" name="kiosk" id="">  
                      <option value="請選擇">請選擇</option> 
						<?php
							foreach($host_arr as $v) {
								$opt_key = $v['id'];
								$opt_val = $v['name'];
								$select  = ($opt_key == $mode) ? 'selected' : '';								
								echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
							}
						?>
                    </select>
                </div>            

                <br>
								<button type='submit' class='btn btnfont-30 btn-primary2 text-white col-lg-4 offset-lg-4'>送出命令</button>
						</div>
				</form>
	  </div>
		<br>
    <!-- SEARCH 工程模式設定 END-->
    
    <!--北護版的form code
    <section class='content'>
        <form action="machine_kiosk_code.php" method="post"  enctype='multipart/form-data'>
          <div class="form-group">
            <label for="exampleInputEmail1">請先選擇命令：</label>      
            <?php //echo form_select("var_list","var_type","工程模式","machine_code","ChangeValues");  ?>
          </div>     
          <div style="display: none;" class="number_value form-group"> 
            <label for="exampleInputEmail1">輸入數值：(只有「手動更新批次號」及「下載指定FTP資料」會需要填入任一數值，其他命令可跳過此項)</label>
            <input class="form-control" type="number" name="number_value" value="1">
          </div>
          <div  style="display: none;" class="KioskName form-group">
            <label for="exampleInputEmail1">選擇棟別：</label>
            <select class=" form-control" name="kiosk" id="">  
                <option value="--">請選擇</option> 
                <option value="A">A</option>  
                <option value="B">B</option> 
            </select>
          </div>
          <div style=";" class="ComputerName form-group">
            <label for="exampleInputEmail1">選擇電腦：</label>   
            <select  class=" form-control" name="computer" id=""> 
                <option value="--">請選擇</option> 
                <option value="M0">M0</option> 
                <option value="M1">M1</option> 
                <option value="M2">M2</option> 
                <option value="M3">M3</option> 
            </select> 
          </div>
          <div class="box-footer">
            <input  onclick="return confirm('你確定要送出嗎？')" id="loading-body2-btn" class=" btn btn-primary" type='submit' value='送出命令' name='send'>
          </div>
        </form>

    </section> -->

    <!--TEST END-->



        <!--Table--->
        <h1 class="mb-2 mt-2 font-weight-bold">工程後台操作命令</h1>
        <div class="my-4 text-right">
						<button type="submit" onclick="return confirm('確認提示\n命令將移除\n您確定要移除嗎?');" class="btn btn btnfont-30 text-white btn-primary2 col-lg-2">
              <i class='fas fa-trash-alt'></i>
              <span>刪除</span>
            </button>
		    </div>
      <div class="table-responsive">
		      <form action="model/host_commands_del.php" method="post">
					
                      <table class="table  text-center font-weight-bold">
                        <thead class="thead-green">
                        <tr class="text-center">
						              <th scope="col">建立日期</th>
                          <th scope="col">棟別</th>
                          <th scope="col">電腦</th>
                          <th scope="col">命令</th>
                          <th scope="col">M0~M3</th>
                          <th scope="col">
                            <div class="form-check">
                              <input type="checkbox" class="form-check-input" id="all_check" onclick="all_checked()"> 
                              <label class="form-check-label ch-top" for="all_check">批次刪除</label>
                            </div>
                          </th>
                        </tr>
                        </thead>
                        <tbody class="kiosk">
						<?php
						foreach($data_arr as $v) {
						?>
                          <tr>
                            <td><?php echo $v['add_date']?></td>
                            <td><?php echo $host_arr[0]['dong'] ?></td>
                            <td><?php echo $v['computer_name']?></td>
                            <td><?php echo $v['c_code']?></td>
                            <td><?php echo $v['M0'].' '.$v['M1'].' '.$v['M2'].' '.$v['M3']?></td>
                            <td>
                              <div class="form-check">
                                  <input type="checkbox" class="form-check-input" id="defaultCheck<?php echo $v['id']?>" name="selected_id[]" value="<?php echo $v['id']?>"> 
                                  <label class="form-check-label" for="defaultCheck<?php echo $v['id']?>"></label>
                              </div>
                            </td>
                          </tr>						
						<?php	
						}
						?>

                          </tbody>
                      </table>				
								
		      </form>
      </div>
      <!--跳頁-->
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
<!-- /.container-fluid -->

<!--checkbox-->
<script>

function all_checked() {	
	$('.form-check-input').prop('checked', $('#all_check').prop('checked'));
}

</script>

<?php  
    include('includes/footer.php');
?>