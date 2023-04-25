<?php 
    include('includes/header.php');
    include('includes/nav.php');
	include('includes/scripts.php');
	$sql = "
		SELECT CONCAT(b.Title,'：',b.`name`) AS title,a.power_status,c.custom_var, a.rate, a.update_date
		FROM room_study_situation a
		INNER JOIN room b ON a.room_id=b.id
		LEFT JOIN custom_variables c ON b.`mode`=c.custom_id AND c.custom_catgory='mode'
		ORDER BY a.room_id ASC
	";
	$data = func::excSQL($sql, $PDOLink, true);
	$inner_html = '';
	foreach($data as $v){
	$def_rate = $v['rate']; //預設：抓room_id最後一間的rate值
	if($v['power_status'] == '1')
	{
		$def_rate = $v['rate'];
		$power_status = '使用中';
		$css = "<i class='fas fa-circle' style='color:#24D354'></i>&nbsp";
	}else{
		$power_status = '未使用';
		$css = '';
	}
		$rate = ($v['custom_var'] != '計費' ) ? 0 : $v['rate'];
		$inner_html .="<div class='col-lg-3 mb-4'>
			<div class='card shadow text-green fz-18 h-100'>
				<div class='card-header card-green border-0'>
					<h1 class='m-0 font-weight-bold text-center text-green'>{$v['title']}</h1>
				</div>
				<div class='card-body'>
					<div class='row study-rate text_normal justify-content-center'>
						<ul class='col-auto'>";
							if($v['custom_var'] == '計費'){ 
								$inner_html .="<li>狀態</li>"; 
							}else{  
								$inner_html .="<br>";  
							}
$inner_html .= "<li>模式</li>
							<li>收費</li>
						</ul>
						<ul class='col-8 text-right'>";
						if($v['custom_var'] == '計費') {
							$inner_html .="<li>{$css}{$power_status}</li>";  
						}else{  
							$inner_html .="<br>";  
						}
$inner_html .= "<li class='orange'>{$v['custom_var']}</li>
							<li>NT$  {$rate} /HR</li>
						</ul>
					</div>
					<hr class='hr-style'>
					<p class='text_normal'>更新時間：{$v['update_date']}</p>
				</div>
			</div> 
		</div> "; 
	}
	
?>
<!--研習室模式設定BK-->

<div class="container-fluid">
	<h1 class="mb-2 font-weight-bold">收費 & 模式設定</h1>
	<div class="row container-fluid mar-bot50 mar-center2">
		<?php if($_GET['success'] == 1){ ?>
			<div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
			<strong><?php echo $lang->line("index.success_room_settings"); ?>!!</strong>
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
	<div class="row justify-content-start">
		<?php echo $inner_html; ?> 
		<div class="col-lg-4 mb-4">
			<div class="card shadow h-100">
				<div class="card-header">
					<h6 class="m-0 font-weight-bold text-center">選擇設定</h6>
				</div>
				<div class="card-body">
					<form id="form1" action='model/study_mode_upd.php' method='POST'>
						<div class="form-group row">
					
							<label class="label-center col-12">研習室(可複選)</label>
							<select required  class='form-control selectpicker col-8 mx-auto custom-select-lg px-1' size='1' title="請選擇" name='study_area[]' multiple>
							<?php  
								$sql = "SELECT * FROM room WHERE Title = '研習室' ";
								$room = func::excSQL($sql, $PDOLink, true);
								foreach($room as $v){
									echo "<option value={$v['id']}>{$v['name']}</option>";
								}
							?> 
							</select>
						</div>
						<label class="label-center col">模式</label>
						<select required class='form-control col-8 offset-2 input-lg2 ' size='1' name='study_mode'  > 
							<option value="">請選擇</option>
							<?php
									$mode_arr = array('1'=>'計費', '3'=>'免費', '4'=>'停用');
									foreach($mode_arr  as $k=>$r)
									{
										echo " <option value={$k}>{$r}</option>";
									}							
							?>
						</select>
						<div >
							<label class="label-center col mt-3">收費(NT$/HR)</label>
							<input type="number" disabled step="1" min="0" class='form-control col-8 offset-2' size='1' id='def_rate'  value=<?php echo $def_rate; ?>> 
						</div>
						<?php 
							// if($in_use) // 使用中房間
							// {
								// foreach($in_use as $v){
								// print "<input type='hidden' name='use[]' value='{$v}' >";
								// } 
							// }
						?>
						<button type="submit" onclick="return confirm('使用中時切換模式，會影響使用權益\n是否確認更新?')"  class="btn  btn-h-auto text-white btnfont-30  btn-martop20 font-weight-bold btn-primary2 col-sm-6 offset-sm-3 col-8 offset-2" >
								確認更新
						</button> 							
					</form>
				</div>
			</div>
		</div>

	</div> 
</div> 
<!--研習室模式設定BK END-->

<script>
 
// function send()
// { 	  
	// if(confirm('使用中時切換模式，會影響使用權益\n是否確認更新?'))
	// {
		// if($('#study_area').val() == '' ) { alert('選項請勿空白'); return false };
		// $('#form1').prop('action','model/kit_mode_upd.php');
		// $('#form1').prop('method','POST');
		// $('#form1').submit();
	// }
// }
</script>

<?php include('includes/footer.php'); ?>