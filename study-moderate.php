<?php  
	include('header_layout.php');
	include('nav.php');
	include('chk_log_in.php');

	$sql = "
		SELECT CONCAT(b.Title,'：',b.`name`) AS title,a.power_status,c.custom_var, a.rate, a.update_date
		FROM room_study_situation a
		INNER JOIN room b ON a.room_id=b.id
		LEFT JOIN custom_variables c ON b.`mode`=c.custom_id AND c.custom_catgory='mode'
		WHERE b.Title = '研習室'
		ORDER BY a.room_id ASC
	";
	$data = func::excSQL($sql, $PDOLink, true);
	$inner_html = '';
	foreach($data as $v){
	if($v['power_status'] == '1')
	{
		$power_status = '使用中';
		$css = "<i class='fas fa-circle' style='color:#24D354'></i>&nbsp";
	}else{
		$power_status = '未使用';
		$css = '';
	}
		$rate = ($v['custom_var'] != '計費' ) ? 0 : $v['rate'];
		$inner_html .="
			<div class='col-lg-6'>
				<div class='card shadow text-green fz-18 mb-4'>
					<div class='card-header card-green border-0'>
						<h1 class='m-0 font-weight-bold text-center text-green'>{$v['title']}</h1>
					</div>
					<div class='card-body'>
						<div class='row study-rate text_normal  justify-content-center'>
							<ul class='col-auto'>" ;
								if($v['custom_var'] == '計費'){ 
									$inner_html .="<li>狀態</li>"; 
								}else{  
									$inner_html .="<br>";  
								}
								$inner_html .="<li>模式</li>
								<li>收費</li>
							</ul>
							<ul class='col-8 text-right'>";
								if($v['custom_var'] == '計費') {
									$inner_html .="<li>{$css}{$power_status}</li>";  
								}else{  
									$inner_html .="<br>";  
								}	
									$inner_html .="<li class='orange'>{$v['custom_var']}</li>
									<li>NT$ {$rate} /HR</li>
							</ul>
						</div>
						<hr class='hr-style'>
						<p class='text_normal'>更新時間：{$v['update_date']} </p>
					</div>
				</div> 
			</div>	
			";		
	}
?>

<section id="main" class="wrapper">
	<div class='col-12 btn-back'><a href='studysetting.php' ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>
	<div class="rwd-box"></div><br><br>
	<div class="container" style="text-align: center;">
		<h1 class="jumbotron-heading text-center">模式設定</h1>
	</div>

	<div class="row container-fluid mar-bot50 mar-center2">
		<?php if($_GET['success'] == 1){ ?>
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
			<strong><?php echo $lang->line("index.success_room_settings"); ?>!!</strong>
			</div>
		<?php } elseif($_GET['success'] == 2) { ?>
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
			<strong><?php echo $lang->line("index.success_room_settings_all"); ?>!!</strong>
			</div>
		<?php } elseif($_GET['error'] == 1) { ?> 
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
			<strong>Error</strong>資料輸入錯誤或不存在
			</div>	
		<?php } elseif($_GET['error'] == 2) { ?> 
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
			請選擇選項
			</div>					
		<?php } ?>
	</div>    
<!--研習室模式設定-->
<div class="container-fluid">   
	<div class="row justify-content-center">
			<!--div class="col-12 alert alert-orange fz-18">
                <p>【製作說明】</p>
					<p>Table:room_study_situation、room</p>
					<p>A.【左方】選擇設定：可複選要設定之模式</p>
					<hr>

					<p>B.【右方】：顯示各研習室當前現況</p>
					<p>1.研習室電力現況(power_status)：只在"計費"時出現，顯示如下</p>
					<p><i class="fas fa-bolt px-1"></i>未使用:power_status=0；</p>
					<p class="text_in_use"><i class="fas fa-bolt px-1"></i>使用中:power_status=1；</p>
					<p>2.收費：計費時才顯示金額，其餘模式一律帶0</p>
					<p>3.更新時間 => 在現況表自建一個欄位</p>
					<hr>

					<p>C.表單防呆機制</p>
					<p>1.皆必填</p>
					<p>2.log：比照電力模組中收費模式的格式紀錄</p>
			</div>-->
			<div class="col-lg-4 mb-4">
				<div class="card shadow" style="height: 96%;">
					<div class="card-header">
						<h6 class="m-0 font-weight-bold text-center">選擇設定</h6>
					</div>
					<div class="card-body">
						<form id="form1"  action='model/study_mode_upd.php' method='POST'>
							<div class="form-group row">
								<label class="label-center col-12">研習室(可複選)</label>
								<select  class='form-control selectpicker col-8 mx-auto custom-select-lg px-1' size='1'    title="請選擇" name='study_area[]' multiple required> 
									<?php   
										$sql = "SELECT * FROM room WHERE Title = '研習室';";
										$room = func::excSQL($sql, $PDOLink, true);
										foreach($room as $v){
											echo "<option value={$v['id']}>{$v['name']}</option>";
										}
									?>
								</select>
							</div>
							<div class="form-group row">
								<label class="label-center col-12">模式</label>
								<select class='form-control selectpicker col-8 mx-auto custom-select-lg px-1' title="請選擇" size='1' name='study_mode'  required> 
									<?php
											$mode_arr = array('1'=>'計費', '3'=>'免費', '4'=>'停用');
											foreach($mode_arr  as $k=>$r)
											{
												echo " <option value={$k}>{$r}</option>";
											}							
									?>
								</select>
							</div> 
							<button type="submit"   onclick="return confirm('使用中時切換模式，會影響使用權益\n是否確認更新?')"  class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold btn-primary2 col-6 offset-3" >
								確認更新
							</button> 						
						</form>
					</div>
				</div>
			</div>
			<div class="col-lg-7 mb-4">
				<?php echo $inner_html; ?> 
			</div>
	</div>
</div>
<!--NEW 研習室模式設定 END-->  
</section> 
<?php include('footer_layout.php'); ?>