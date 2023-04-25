<?php 
	ob_start();
	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');  
	ini_set('max_execution_time', 0); // 最大連線逾時時間 (在php安全模式下無法使用)	 
	set_time_limit(0);
	// ini_set("memory_limit","2048M");	
	
	$pagesize = 10;
	$sql_kw   = "";
	$now_time = date('Y-m-d H:i:s');
	$search   = $_GET['search'];
	$room_total_ele = 0;
	// var_dump($now_month);

	// 每日用電:輸入日期，開始結束都抓同天
	$st_date  = $_GET['st_date'];
	$end_date = date('Y-m-d',strtotime($_GET['st_date']."+1 day"));
	// 給初值
	if($st_date == "") $st_date = date('Y-m-d');
	$dong_html = "";
	$sql = "SELECT * FROM dongname ORDER BY id";
	$dong_data = func::excSQL($sql, $PDOLink, true);
	$dong = $_GET['m_dong'];

	if($_GET['search']){
		$dong_name = "";
		if(count($dong) > 0) {
			$$dong_html = "";			
			foreach($dong_data as $v) {
				$selected = "";
				for($i=0;$i<count($dong[$i]);$i++) {
					$selected = ($dong[$i] == $v['dong']) ? 'selected':'';
					if($selected == 'selected') {
						$dong_name = $v['dong_name'];
						break;
					}
				}
				$dong_html .= "<option value='".$v['dong']."'} {$selected}>{$v['dong_name']}</option>";
			}
		}
		
		$inner_html = "";

		if(!$dong || !$st_date)  die(header('Location: power-consumption-d.php?error=3'));  

		if(isset($_GET['st_date'])) {
			$param_st = array(
			// 每日用電:抓開始~結束日的時間
			":st_date"=> $st_date." 00:00:00",
			":end_date"=> $end_date." 00:00:59",
			":dong" => $dong
			); 
			$sql = "select (select name from room where id = a.room_id) as name,a.amonut as start_amonut,b.amonut as end_amonut,ROUND((b.amonut-a.amonut),2) as use_amonut,DATE_FORMAT(a.update_date,'%Y-%m-%d %H:%i:%s') as start_time,DATE_FORMAT(b.update_date,'%Y-%m-%d %H:%i:%s') as end_time from  room_amonut_log as a
			INNER JOIN (select room_id,max(b.amonut) as amonut,max(b.update_date) as update_date from  room_amonut_log as b where b.update_date >= '".$param_st[':st_date']."' and b.update_date <= '".$param_st[':end_date']."' group by b.room_id) as b on a.room_id = b.room_id
			where a.update_date >= '".$param_st[':st_date']."' and a.update_date <= '".$param_st[':end_date']."' and a.room_id in (SELECT id FROM room where dong = '".$param_st[':dong']."') group by a.room_id order by a.room_id ";  
			$room_data = func::excSQL($sql, $PDOLink, true);
			if($room_data) {
				foreach($room_data as $r) {
					$room_ele = $r['use_amonut']; // 單間房的總度數
					$room_total_ele += $room_ele; // 整棟加總
					$update_st = $r['start_time'];
					$update_ed = $r['end_time'];
					$inner_html .=" 
					<div class='col-lg-4 card-group'>
						<div class='card mb-4 card-green text-green fz-18 h-auto'>
							<div class='py-2 nowsystem'>
								<ul class='px-1'>
									<li >房號：<span id='dong'>{$r['name']}</span></li>
									<li >開始時間：<span id ='st_time'>{$update_st}</span></li>
									<li >結束時間：<span id ='ed_time'>{$update_ed}</span> </li>
									<li class='total-meter' >用電總計(110V):<span id ='total'>{$room_ele}</span> 度</li>
									<!--新増220使用度數資料 -->
									<li >開始時間：<span id ='st_time'>{$update_st}</span></li>
									<li >結束時間：<span id ='ed_time'>{$update_ed}</span> </li>
									<li class='total-meter'>用電總計(220V):<span id ='total'>{$room_ele}</span> 度</li>
								</ul>
							</div>
						</div>
					</div>	 
					";
				}  	
			// print '總計'.$room_total_ele.'<br>';
			// print $update_st .'<br>';
			// print $update_ed .'<br>';
			} else {
				die(header("Location: power-consumption-d.php?error=5&m_dong=".$dong."&st_date=".$st_date."&end_date=".$end_date));  
			}

		} else {
			die(header("Location: power-consumption-d.php?error=3&m_dong=".$dong."&st_date=".$st_date."&end_date=".$end_date));  
		}
	} else {
		if($dong_data) {
			foreach($dong_data as $v) {
				$selected = "";
				for($i=0;$i<count($dong[$i]);$i++) {
					$selected = ($dong[$i] == $v['dong']) ? 'selected':'';
					if($selected == 'selected') {
						//echo "select data:".$dong[$i]."<BR>";
						$dong_name = $v['dong_name'];
						break;
					}
				}
				$dong_html .= "<option value='".$v['dong']."'} {$selected}>{$v['dong_name']}</option>";
			}
		}
	}
?> 
 <section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='powersearch.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">用電查詢</h1>
    </div>
	
	<div class="row mar-center2 mb-4" style="margin: 0 auto;">
		<?php if($_GET['error'] == 1){ ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>開始月份無資料</strong>
			</div>
		<?php } elseif ($_GET['error'] == 2) { ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>結束月份無資料</strong>
			</div>			
		<?php } elseif ($_GET['error'] == 3) { ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>請勿空白</strong>
			</div>
		<?php } elseif ($_GET['error'] == 4) { ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>年/月份錯誤</strong>
			</div>
		<?php } elseif ($_GET['error'] == 5) { ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>查無資料</strong>
			</div>	
		<?php } elseif ($_GET['error'] == 6) { ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-9" role="alert">
			<strong>無資料匯出</strong>
			</div>			
		<?php } elseif ($_GET['success']) { ?>
			<div style="margin: 0 auto; text-align: center;" class="alert alert-success col-lg-9" role="alert">
			<strong>Success 成功設置！！</strong>
			</div>
		<?php } ?>
	</div>
	<!--標籤切換頁-->
	<div class="container">
		<ul class='nav nav-tabs nav-border'>
			<li class='nav-item'> 
				<a class='nav-link card card-border active' id='tab_day' href='power-consumption-d.php' role='tab' aria-selected='false'>
					<span class='h5 mb-0 font-weight-bold '>每日用電</span>
				</a>    
			</li>
			<li class='nav-item'>
				<a class='nav-link card card-border' id='tab_month' href='power-consumption-m.php' role='button' aria-selected='false'>
					<span class='h5 mb-0 font-weight-bold'>每月用電</span>
				</a>           
			</li>
			<li class='nav-item'> 
				<a class='nav-link card card-border' id='tab_year' href='power-consumption-y.php' role='button' aria-selected='false'>
					<span class='h5 mb-0 font-weight-bold '>每年用電</span>
				</a>    
			</li>
		</ul>
		<hr class='view'>
	</div>
	<!--標籤切換頁 END-->

	<!-- 查詢&顯示結果 -->
	<div class="container">
			<form id='mform2' method="get" class='col-12'>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label label-right">棟別</label>	
						<div class="col-sm-9 form-inline">
							<select class="col form-control selectpicker show-tick" title='請選擇'  size="1" name="m_dong"  >
								<?php echo $dong_html;?>
							</select>
						</div>
					</div>
					<div class="form-group row select-mar3">
						<label class="col-sm-2 col-form-label label-right">輸入日期</label>
						<div class="col-sm-9"> 
							<input class="form-control form-control2" type="date" placeholder="yyyy-mm-dd" size="20" name="st_date" value="<?php echo $st_date ?>">
						</div>
					</div>
					<br>
					<input type='hidden' id='end_date' name='end_date'  value='<?php echo $end_date ?>'>
					<input type='hidden' name='search' value='1'>
					<div class='col-12'>
						<button type='submit' id="search-btn" class='btn  btn-loginfont btn-primary2  col-4 offset-4'><?php echo $lang->line("index.confirm_query") ?></button>
					</div>
			</form>
			<?php 
				if($_GET['search'] == 1) {
					print 	"<div class='col-12 mt-4'>";
				} else {
					print 	"<div class='col-12 mt-4' style='display:none'>";
				}
			?>
	
			<h1 class="jumbotron-heading text-center h1-mar">查詢結果</h1>
			<div class=" text-right ">
				<button type="button" class="btn btn-loginfont btn-primary2 col-sm-2 offset-sm-10 mb-3" onclick='export2excel1()'><?php echo $lang->line("index.export") ?></button>
			</div>
			<h5 class="text-gray-900 font-weight-bold">更新時間:<?php echo $now_time ?></h5>
			<div id="power-total" class="col-12 alert alert-info text-green"><?php echo $dong_name?>總計用電：<?php echo $room_total_ele ?></div>
			<div class="row">
				<?php echo $inner_html; ?>
			</div>
	</div>
	<!-- 查詢&顯示結果 END-->

</section>

<script>
	$('#search-btn').click(function() {
			$('body').loading({
		stoppable: false,
		message: '資料較龐大加總計算中，請耐心等候勿關閉本頁...',
		theme: 'dark'
		}); 
	});

	function export2excel1() 
	{	
		var dong=$('[name="m_dong"]').val();
		var st_date=$('[name="st_date"]').val();
		var end_date=$('#end_date').val();
		var total=$('#power-total').text();
		location.replace("model/power-consumption-d_report.php?dong="+dong+"&st_date="+st_date+"&end_date="+end_date+"&total="+total);
	}
</script>

<?php include('footer_layout.php'); ?>