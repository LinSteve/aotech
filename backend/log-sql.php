<?php
    include('includes/header.php');
    include('includes/nav.php');
	include('includes/scripts.php');
	
	$pagesize    = 10;
	
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';
	
	$sql = "SELECT COUNT(*) as 'count' FROM system_setting 
			WHERE computer_name = 'Web' OR title = '工程模式'";
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
	
	$sql = "SELECT * FROM system_setting 
			WHERE computer_name = 'Web' OR title = '工程模式' 
			ORDER BY id DESC LIMIT ".($page-1)* $pagesize . ",".$pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
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
		<h1 class="mb-2 font-weight-bold">SQL Log</h1>
		<!--
        <p class="text-lg text-center font-weight-bold NG-color">
          比照前台Log，帶出後端資料庫的修改紀錄
		</p>
		-->
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
							<th scope="col">M0</th>
                            <th scope="col">M1</th>
                            <th scope="col">M2</th>
                            <th scope="col">M3</th>
                            <th scope="col">M4</th>
                            <th scope="col">M5</th>
                            <th scope="col">修改類別</th>
                          </tr>
                        </thead>
                        <tbody class='log'>
						<?php
						foreach($data as $v) {
							$row_count = ($prepage * $pagesize) + ++$j;
							$show_date = date($date_format.' '.$time_format, strtotime($v['add_date']));
							$show_msg  = $v['c_code'];
							$show_cate = $v['title'].$v['computer_name'];
							$M0  = $v['M0'];
							$M1  = $v['M1'];
							$M2  = $v['M2'];
							$M3  = $v['M3'];
							$M4  = $v['M4'];
							$M5  = $v['M5'];
						?>
						  <tr>
							<td><?php echo $row_count ?></td>
                            <td><?php echo $show_date ?></td>
                            <td><?php echo $show_msg  ?></td>
							<td><?php echo $M0 ?></td>
                            <td><?php echo $M1 ?></td>
                            <td><?php echo $M2 ?></td>
                            <td><?php echo $M3 ?></td>
                            <td><?php echo $M4 ?></td>
                            <td><?php echo $M5 ?></td>
                            <td><?php echo $show_cate ?></td>
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
		
		$('#mform1').prop('action', 'model/sql_list_dispose.php');
		$('#mform1').prop('method', 'get');
		
		$('#mform1').submit();
	}
}

function export_file() {

	$('#mform1').prop('action', 'model/sql_list_download.php');
	$('#mform1').prop('method', 'get');
	
	$('#mform1').submit();
}
</script>

<?php  
    include('includes/footer.php');
?>