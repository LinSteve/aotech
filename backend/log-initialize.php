<?php
    include('includes/header.php');
    include('includes/nav.php');
	include('includes/scripts.php');
	
	$pagesize = 10;
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';
	
	$sql = "SELECT COUNT(*) as 'count' FROM room_hardware_initialization ";
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
		$pageurl .= "<a href='?page=1'>".$lang->line("index.home").
					"</a> | <a href='?page={$prepage}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}'>".$lang->line("index.next_page").
					"</a> | <a href='?page={$pagenum}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT * FROM `room_hardware_initialization` ORDER BY id DESC LIMIT ".($page-1)* $pagesize . ",".$pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	$data_type = array('1' => '登入', 'import' => '匯入');
?>

<!-- Begin Page Content -->
<div class="container-fluid">

        <div class="row container-fluid mar-bot50">
        <?php if($_GET['success'] == 1){ ?>
            <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
            <strong>清空完成！</strong>
            </div>
        <?php } elseif($_GET['error'] == 1){ ?> 
            <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
            <strong>沒有可匯出的資料！</strong>
            </div>	
        <?php } ?>
        </div>  

        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">初始化 Log</h1>
        <!--
		<p class="text-lg text-center font-weight-bold NG-color">
          放入門禁、電梯、房間初始化Log
		</p>-->
		
		<form id='mform1'></form>
        <div class="my-4 text-right">
            <button type="button" onclick='dispose_list()' class="btn btn btnfont-30 text-white btn-primary2 col-lg-2">
              <i class='fas fa-trash-alt'></i>
              <span>清空</span>
			</button>
			&nbsp;
			<button type="button" onclick='export_file()' class="btn btn btnfont-30 text-white btn-primary2 col-lg-2">
              <i class="fas fa-arrow-circle-down"></i>
              <span>匯出</span>
            </button>
		</div>
        <!--Table--->
        <div class="table-responsive">
		
                      <table class="table  text-center font-weight-bold">
                        <thead class="thead-green">
                          <tr class="text-center">
                            <th scope="col">#</th>
                            <th scope="col">建立日期</th>
                            <th scope="col">修改訊息</th>
                            <th scope="col">修改類別</th>
                          </tr>
                        </thead>
                        <tbody class='log'>
						<?php						
						foreach($data as $v) {
							$row_count = ($prepage * $pagesize) + ++$j;
							$showtime  = date($date_format.' '.$time_format, strtotime($v["add_date"]));
						?>
                          <tr>
                            <td><?php echo $row_count ?></td>
                            <td><?php echo $showtime ?></td>
                            <td><?php echo $v['InitializationMessage'] ?></td>
                            <td><?php //  ?></td>
                          </tr>
						<?php						
						}
						?>      
                        </tbody>
                      </table>
					  

		</div>
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

<script>

function dispose_list() {
	
	if(confirm("確認提示\n您確定要清空嗎?")) {
		
		$('#mform1').prop('action', 'model/log_list_dispose.php');
		$('#mform1').prop('method', 'get');
		
		$('#mform1').submit();
	}
}

function export_file() {

	$('#mform1').prop('action', 'model/log_list_download_hardware.php');
	$('#mform1').prop('method', 'get');
	
	$('#mform1').submit();
}
</script>

<?php
    include('includes/footer.php');
?>