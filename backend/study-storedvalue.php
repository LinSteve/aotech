<?php
    include('includes/header.php');
    include('includes/nav.php');
	
 	$pagesize = 10;
	
	$sql_kw      = "";
	$date_format = 'Y/m/d';
	$time_format = 'H:i:s';
	
 	$cname       = $_GET['cname'];
 	$id_card    = $_GET['id_card'];
 	$room_num    = $_GET['room_strings'];
	$start_date  = $_GET['start_date'];
	$end_date    = $_GET['end_date'];
	$sort		 = $_GET['sort'];
	$search      = $_GET['search'];
	
	if($id_card) { $sql_kw .= " AND m.`id_card` like '%".trim($id_card)."%' "; }
	if($cname)    { $sql_kw .= " AND m.`cname` like '%".trim($cname)."%' "; }
	if($room_num) { $sql_kw .= " AND r.name LIKE '%".trim($room_num)."%' "; }
	if($sort)	  { $sql_kw .= " AND r.`name` = '".trim($room_num)."' "; }
	
	if($start_date) { 
		$s_time  = date('Y-m-d', strtotime($start_date));
		$sql_kw .= " AND e.add_date > '{$s_time}' "; 
	}
	
	if($end_date)   { 
		$e_time  = date('Y-m-d', strtotime($end_date." +1 day"));
		$sql_kw .= " AND e.add_date < '{$e_time}' "; 
	}
	
	$sql = "
		SELECT count(*) as 'count' FROM `ezcard_record` e
		LEFT JOIN member m ON e.member_id = m.id
		LEFT JOIN room r ON r.id = e.room_id WHERE 1 AND r.Title = '研習室'  ".$sql_kw;

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
		$pageurl .= "<a href='?page=1&cname={$cname}&id_card={$id_card}&room_strings={$room_num}&start_date={$start_date}&end_date={$end_date}&search={$search}'>".$lang->line("index.home").
					"</a> | <a href='?page={$prepage}&cname={$cname}&id_card={$id_card}&room_strings={$room_num}&start_date={$start_date}&end_date={$end_date}&search={$search}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}&cname={$cname}&id_card={$id_card}&room_strings={$room_num}&start_date={$start_date}&end_date={$end_date}&search={$search}'>".$lang->line("index.next_page").
					"</a> | <a href='?page={$pagenum}&cname={$cname}&id_card={$id_card}&room_strings={$room_num}&start_date={$start_date}&end_date={$end_date}&search={$search}'>".$lang->line("index.last_page")."</a>";
	}
	
	$sql = "SELECT e.*, m.username, m.cname, r.`name`, m.balance, m.berth_number ,m.id_card,m.id_card
			FROM `ezcard_record` e
			LEFT JOIN member m ON e.member_id = m.id
			LEFT JOIN room r ON r.id = e.room_id
			WHERE 1  AND r.Title = '研習室' ".$sql_kw." ORDER BY e.add_date DESC LIMIT ".($page-1)* $pagesize . ",".$pagesize;
					// echo $sql ;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll(); 
	$sort_map = array("Stored" => "付款", "Refund" => "退費");
?>

<!-- Begin Page Content -->
<div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">研習卡儲值紀錄</h1>

		<div class="row container-fluid mar-bot50 mar-center2">
			<?php if($_GET['error'] == 1){ ?>
				<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
					<strong>Error</strong>資料輸入錯誤或不存在
				</div>					
			<?php } ?>
		</div>   
		<!-- SEARCH 學生儲值紀錄-->
		<div class='col-12'>
				<form id='mform1' action="" method="get">

					<div class='form-group row '>
						<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>房號</label>
						<div class='col-sm-8 input-group-lg'> 
							<input  type='text' class='form-control' name='room_strings' placeholder='全部' value='<?php echo $room_num ?>'>
						</div>
					</div>

					<div class='form-group row '>
						<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>姓名</label>
						<div class='col-sm-8 input-group-lg'> 
							<input   type='text'  class='form-control' name='cname' placeholder='全部' value='<?php echo $cname ?>'>  
						</div>
					</div>
															

					<div class='form-group row '>
						<label for='exampleFormControlInput1'  class='col-sm-2 col-form-label label-right'>卡號</label>
						<div class='col-sm-8 input-group-lg'> 
							<input  type='text' maxlength='10' class='form-control  col' name='id_card' placeholder='全部' value='<?php echo $id_card ?>'>  
						</div>
					</div>
				


					<div class='form-group row'>
						<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>開始時間</label>
						<div class='col-sm-8 input-group-lg'> 
							<input  type='date' class='form-control date-pd' name='start_date' value='<?php echo $start_date ?>'>										
						</div>
					</div>

					<div class='form-group row'>
						<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right'>結束時間</label>
						<div class='col-sm-8 input-group-lg'> 
							<input  type='date'  class='form-control date-pd' name='end_date' value='<?php echo $end_date ?>'>
						</div>
					</div>

					<div class='form-group row'>
						<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right pd-top25'>狀態</label>
						<div class='col-sm-8 form-inline'> 
							<select  class='col form-control selectpicker custom-select-lg' size='1' name='sort'>
								<option value=''>全部</option>
								<?php
								foreach($sort_map as $k => $v) {
									$select = ($k == $sort) ? 'selected' : '';
									echo "<option value='{$k}' {$select}>{$v}</option>";
								}
								?>
							</select>
						</div>
					</div>					
					
					<br>
					<input type='hidden' name='search' value='1'>
					<button type='button' onclick='export_list()' class='btn btnfont-30 btn-primary2 text-white col-sm-4 offset-sm-4'>查詢</button>
				</form>
	    </div>
		<br>
		<!--
		<p class="text-lg text-center font-weight-bold NG-color">
			可參考北護的「學生儲值使用紀錄」處理<br>
			BeforeValue：186(悠遊卡儲值/退費前的總額186)<br>
			PayValue：100(到系統儲值或退費的值，系統會以此欄進行電力扣款)<br>
			SavedValue：86(悠遊卡儲值/退費後的總額)<br>
			如下表Sample所示
		</p>
		-->
		<!-- SEARCH 學生儲值紀錄 END-->
		<div class=" text-right " style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
				<button type="button" onclick='export_file()' class="btn btnfont-30 text-white btn-primary2 col-sm-2">匯出</button>
		</div>
		<br>

        <!--Table--->
        <div class="table-responsive" style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
                      <table class="table  text-center font-weight-bold">
                        <thead class="thead-green">
                        <tr class="text-center">
						  <th scope="col">#</th>
                          <th scope="col">付款日期</th>
                          <th scope="col">姓名/卡號</th>
                          <th scope="col">房號</th>
                          <th scope="col">狀態</th>
                          <!--<th scope="col">儲值/退款後餘額</th>-->
                          <th scope="col">BeforeValue</th>
                          <th scope="col">PayValue</th>
                          <th scope="col">SavedValue</th>
                        </tr>
                        </thead>
                        <tbody>
						<?php
						foreach($data as $row) 
						{
							$row_count = ($prepage * $pagesize) + ++$j;
							$showtime  = date($date_format.' '.$time_format, strtotime($row["add_date"]));
							$showname  = $row['cname'];
							$showname .= " / ";
							$showname .= $row['id_card']; 
							$showsort  = $sort_map[$row["Sort"]];
						?>
                          <tr>
                            <td><?php echo $row_count ?></td>
                            <td><?php echo $showtime ?></td>
                            <td><?php echo $showname ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $showsort ?></td>
                            <td><?php echo $row['BeforeValue'] ?></td>
                            <td><?php echo $row['PayValue'] ?></td>
                            <td><?php echo $row['SavedValue'] ?></td>
                          </tr>

						<?php
						}
						?>
                          </tbody>
                      </table>
					  

		</div>
		<div class="row " style="display:<?php echo ($search != '') ? 'block' : 'none'?>">
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

function export_list() {
	
	$('#mform1').prop('action', 'study-storedvalue.php');
	$('#mform1').prop('method', 'get');
	
	$('#mform1').submit();
}

function export_file() {

	$('#mform1').prop('action', 'model/study_stored_download.php');
	$('#mform1').prop('method', 'get');
	
	$('#mform1').submit();
}
</script>


<?php
    include('includes/scripts.php');
    include('includes/footer.php');
?>