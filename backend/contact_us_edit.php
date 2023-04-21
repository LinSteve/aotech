<?php
    include('includes/header.php');
    include('includes/nav.php');
	include('includes/scripts.php');
	
	$id  = $_GET['id'];
	$sql = "SELECT * FROM content_us WHERE id = '{$id}'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$chk_data = $rs->fetch();

	$u_date   = $chk_data['update_date'];
	$replier  = $chk_data['replier'];
	$status   = $chk_data['data_type'];
	$contact  = $chk_data['contact'];
	$remark   = $chk_data['remark'];
	
	$sql = "SELECT * FROM `custom_variables` WHERE `custom_catgory` = 'contact_status'";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data_type = $rs->fetchAll();
	
	$sql = "SELECT id FROM content_us ORDER BY add_date DESC";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$id_tmp = $rs->fetchAll();
	
	$pos = 0;
	$pos_f;
	$pos_p;
	
	foreach($id_tmp as $v) {
		
		if($v['id'] == $id) {
			$pos_p  = $pos + 1 >= sizeof($id_tmp) ? $id_tmp[$pos]['id'] : $id_tmp[$pos + 1]['id'];
			$pos_f  = $pos - 1 < 0 ? $id_tmp[$pos]['id'] : $id_tmp[$pos - 1]['id'];
			continue;
		}
		
		$pos++;
	}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">客服人員-處理狀況</h1>
    	<div class="container-fluid mar-bot25 mar-center2">
			<?php if($_GET['success'] == 3){ ?>
				<div style="margin: 0 auto; text-align: center; " class="alert alert-danger" role="alert">
					<!--原先舊的：操作:進入客服人員-處理狀況後，點送出>彈窗按下取消才會出現
					<strong>提醒:未處理時，無法送出喔！</strong>-->
				</div>
			<?php } elseif ($_GET['success'] == 4) { ?>
				<div style="margin: 0 auto; text-align: center; " class="alert alert-success" role="alert">
					<strong>已更新，並送出E-mail !!</strong>
				</div>
			<?php } elseif ($_GET['error'] == 3) { ?>
				<div style="margin: 0 auto; text-align: center; " class="alert alert-danger" role="alert">
					<strong>更新失敗!!</strong>
				</div>
			<?php } elseif ($_GET['error'] == 4) { ?>
				<div style="margin: 0 auto; text-align: center; " class="alert alert-danger" role="alert">
					<strong>客服回覆信件發送失敗!!</strong>
				</div>
			<?php } ?>
		</div>
			
	<!-- NEXT Button-->
	<div class='pull-left'>
		<a class='rounded' href='contact_us_edit.php?id=<?php echo $pos_f ?>'>
			<i class='fas fa-chevron-circle-left fa-2x' data-toggle='tooltip' data-placement='bottom' title='較新'></i>
		</a>
	</div>
	<div class='pull-right mb-2'>
		<a class='rounded' href='contact_us_edit.php?id=<?php echo $pos_p ?>'>
			<i class='fas fa-chevron-circle-right fa-2x' data-toggle='tooltip' data-placement='bottom' title='較舊'></i>
		</a>
	</div>
	<div style="margin: 0 auto; text-align: center; clear:both;" class="alert alert-info mb-4" role="alert">
			說明：點上方<i class="fas fa-chevron-circle-left"></i>或<i class="fas fa-chevron-circle-right"></i>:可編輯下一筆(較舊/新)客服信的處理狀況<br>
	</div>	
	<div class='row'>

		<!-- 客服信內容 -->
		<div class='col-xl-5 mb-4'>
			<div class='card shadow'>
				<div class='card-body'>
					<div class='mb-2 font-weight-bold text-gray-800 text-center'>
                        <h5 class='text-green'>客服信內容</h5>
						<hr>
						<ul class='contact-us'>
							<li>報修日期：<?php echo $chk_data['add_date'] ?></li>
							<li>房號/床號：<?php echo $chk_data['room_number'] ?></li>
							<li>姓名/學號：<?php echo $chk_data['title'].'/'.$chk_data['username_number'] ?></li>
							<li>電話/e-mail：<?php echo $chk_data['phone'].'/'.$chk_data['email'] ?></li>
							<li>報修-儲值主機：<?php echo $chk_data['host_type'].' '.$chk_data['host_other'] ?></li>
							<li>報修-房內卡機：<?php echo $chk_data['room_type'].' '.$chk_data['room_other'] ?></li>
						</ul>
					</div>

				</div>
			</div>
	    </div>
		<br>
		<!-- 客服信內容 END-->

		<!-- 處理狀況 -->
		<div class='col-xl-7 mb-4'>
			<div class='card shadow h-100'>
				<div class='card-body'>
					<div class="mb-2 font-weight-bold text-gray-800 text-center">
                        <h5 class="text-green">編輯處理狀況</h5>
						<hr>
					</div>
					<form id='mform1' action="model/contact_us_process.php" method="post">

						<div class='form-group'>
							<label for='exampleFormControlInput1' class='col-form-label label-left'>處理日期</label>
							<div class='input-group-lg'> 
								<input  type='date' required="required" class='form-control date-pd' name='update_date' value='<?php echo $u_date ?>' id='TitleValue'>										
							</div>
						</div>

						<div class='form-group'>
							<label for='exampleFormControlInput1' class='col-form-label label-left'>處理人員</label>
							<div class='input-group-lg'> 
								<input  type='text' required="required" class='form-control' name='replier' value='<?php echo $replier ?>' id='UsernameValue'>
							</div>
						</div>

						<div class='form-group'>
							<label for='exampleFormControlInput1' class='col-form-label label-left'>處理狀態</label>
							<div class='form-inline'> 
								<select required class='col form-control selectpicker custom-select-lg' size='1' name='status' id='status'>
								<?php
									foreach($data_type as $v) {
										$opt_key = $v['custom_id'];
										$opt_val = $v['custom_var'];
										$select  = ($opt_key == $status) ? 'selected' : '';
										echo "<option value='{$opt_key}' {$select}>{$opt_val}</option>";
									}
								?>
								</select>
							</div>
						</div>
						
						<div class='form-group'>
							<label for='exampleFormControlInput1' class='col-form-label label-left'>備註</label>
							<div class='input-group-lg'> 
								<input type='text' class='form-control' name='remark' value='<?php echo $remark ?>' placeholder="若狀態為「其它」請註明，如：通知電工、外修等">
							</div>
						</div>
						
						<div class="form-group">
							<label for="exampleFormControlInput1" class="col-form-label label-right">制式回信</label>	
								<select class="col form-control selectpicker custom-select-lg" title="請選擇"   size="1"  name="select_contents" >
									<option value="已處理完成。若使用上有任何問題，再請告知。謝謝！">已處理完成。若使用上有任何問題，再請告知。謝謝！</option>
									<option value="已處理完成，若使用上仍有問題，請留下您的連絡電話，讓我們儘快能協助處理，謝謝">已處理完成，若使用上仍有問題，請留下您的連絡電話，讓我們儘快能協助處理，謝謝</option>
								</select>
						</div>
						
						<div class='form-group'>
							<label for='exampleFormControlInput1' class='col-form-label label-right'>客製回信</label>
							<textarea name='mail_content' class="form-control" rows="10" style="resize:none"><?php // echo $contact ?></textarea>
						</div><br>
						<input type='hidden' name='id' value='<?php echo $id ?>'>
						<input type='hidden' name='search' value='1'>
						<button type='button' class='btn btnfont-30 btn-primary2 text-white col-sm-4 offset-sm-4' onclick="check_submit()">確認送出</button>
					</form>
				</div>
			</div>
	    </div>
		<!-- 處理狀況 END-->

	</div>
</div>
<!-- /.container-fluid -->

<script>
//click後，只要處理日期、或人員為空白、或狀態為未處理，一律顯示彈窗
function check_submit() {
//$('#loading-body2-btn').click(function() {

//取值
username = $("#UsernameValue").val();
title = $("#TitleValue").val();
status = $("#status").val();

//防呆判斷：漏填=>跳警告窗；無漏填=>跳驗證窗，防客服人員發現自己編輯內容有誤想改
if (username =="" || title=="" ||  status == '1') {
	alert('【提示】未填寫完無法寄信，請確認：\n1.處理日期\n2.處理人員\n3.處理狀態:未處理不可寄信！');
	return false;

} else if(confirm('確認送出?') == true) {
	$('#mform1').submit();
}else{
	return false;
}

//});
}
</script>
<!--<script>
之前寫的:經實際操作測試會有防呆漏洞

var msg_unfinished = "提醒:未處理時，無法送出喔！";

function check_submit() {
	
	if(confirm('確認送出?')) {
		
		if($('#status').val() == '1') {
			// alert(msg_unfinished);
			var inpObj  = document.getElementById("status");
			inpObj.setCustomValidity('提醒:未處理時，無法送出喔！');
			return false;
		}
	} else {
		return false;
	}

	
}
</script>-->
<?php
    include('includes/footer.php');
?>
<script src="vendor/ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('mail_content');
</script>