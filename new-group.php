<?php 

include('header_layout.php');
include('nav.php');
include('chk_log_in.php');

for($i = 1; $i <= 6; $i++) {
	eval("\$opt".$i." = '';");
}

$sql = "SELECT * FROM `menu_list` WHERE id NOT IN (7,8,11)";
$rs  = $PDOLink->prepare($sql);
$rs->execute();
$tmp = $rs->fetchAll();

foreach($tmp as $v) {
	
	$id        = $v['id'];
	$category  = $v['category'];
	$item_name = $v['item_name'];
	
	switch($category) {
		
		case '1':
			$opt1 .= "<option value='{$id}'>{$item_name}</option>";
			break;
		case '2':
			$opt2 .= "<option value='{$id}'>{$item_name}</option>";
			break;
		case '3':
			$opt3 .= "<option value='{$id}'>{$item_name}</option>";
			break;
		default:
			break;
	}
}

$sql = "SELECT * FROM `door`";
$rs  = $PDOLink->prepare($sql);
$rs->execute();
$tmp = $rs->fetchAll();

foreach($tmp as $v) {
	
	$id        = $v['id'];
	$item_name = $v['name'];
	
	$opt4 .= "<option value='{$id}'>{$item_name}</option>";
}

$sql = "SELECT * FROM `elevator`";
$rs  = $PDOLink->prepare($sql);
$rs->execute();
$tmp = $rs->fetchAll();

foreach($tmp as $v) {
	
	$id        = $v['id'];
	$item_name = $v['name'];
	
	$opt5 .= "<option value='{$id}'>{$item_name}</option>";
}

$sql = "SELECT * FROM `room`";
$rs  = $PDOLink->prepare($sql);
$rs->execute();
$tmp = $rs->fetchAll();

foreach($tmp as $v) {
	
	$id        = $v['id'];
	$item_name = $v['name'];
	
	$opt6 .= "<option value='{$id}'>{$item_name}</option>";
}
?>


<section id="main" class="wrapper">
<div class="col-12 btn-back"><a href="roomgroup.php" ><i class="fas fa-chevron-circle-left fa-3x"></i><label class='previous'></label></a></div>

	<div class="rwd-box"></div><br><br>
	<div class="row container-fluid mar-bot50">
	<?php if($_GET['success'] == 1){ ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-success col-lg-6" role="alert">
		  <strong>新增完成!!</strong> 
		</div>
	<?php } else if($_GET['error'] == 1){ ?>
		<div style="margin: 0 auto; text-align: center;" class="alert alert-danger col-lg-6" role="alert">
		  <strong>新增失敗!! </strong> 
		</div>
	<?php } ?> 
	</div>
	<div class="inner">
		<div class="container">
			<div class="row">

			<!-- 	<a href="RoomStatus3.php"><i class="fas fa-chevron-circle-left fa-3x"></i></a><br> -->
				<div class="col-12">
					<h1 class="jumbotron-heading text-center">建立群組</h1>

					
<!--TEST -->



					<form action='model/member_group_add.php' method='POST'>

						<div class="form-group row">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right" >群組名稱</label>

							<div class="col-sm-9"> 
								<input   type="text" required='required' maxlength='10' class="form-control  col" name="groupname" placeholder="">
    						</div>

						</div>


						<div class="form-group row">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right">用途說明</label>
    						<div class="col-sm-9"> 
								<input   type="text"  maxlength='30' class="form-control  col" name="usage" placeholder="">  
    						</div>
						</div>


<!--CHECK BOX TEST -->
<!--
						<div class="form-group row">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right">後台權限</label>
							<div class="col-sm-9 form-inline ">
		
								<input id="checkbox1" class="" type='checkbox' name='username'>
								<label for="checkbox1" class="col-sm-2   col-form-label label-right " >名單管理
								</label>

								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right checkbox-mar">門禁管理</label>	
								<select class="selectpicker form-control col-sm-2  "  
								data-none-selected-text="請選擇"    size="1"  name="room_numbers_kw"   multiple  >
									<option value="1">查詢</option>
									<option value="1">管理設定</option>
								</select>

								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right checkbox-mar">電力管理</label>	
								<select class="selectpicker form-control col-sm-2  "  
								data-none-selected-text="請選擇"    size="1"  name="room_numbers_kw"   multiple  >
									<option value="1">查詢</option>
									<option value="1">費率設定</option>
									<option value="2">模式設定</option>
								</select>
								
							</div>
						</div>
-->
<!--CHECK BOX TEST END -->



<!--0420 後台權限複選 TEST  -->

						<?php
						if($_SESSION['admin_user']['username'] == WEBADMIN) {
						?>
						<div class="form-group row">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">後台權限</label>							
							<div class="col-sm-9 form-inline">
								<!--
								<div class="col-sm-4">
								<input id="checkbox2" class="form-control " type='checkbox' name='username'>
								<label for="checkbox2" class=" col-form-label label-center col-7">名單管理</label>	
								</div>
								-->
								
								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">名單管理</label>	
								<select class="selectpicker form-control col-sm-2  "  
								data-none-selected-text="請選擇"  size="1"  name="opt1[]"   multiple  ><?php echo $opt1 ?></select>
								
								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">門禁管理</label>	
								<select class="selectpicker form-control col-sm-2  "  
								data-none-selected-text="請選擇"  size="1"  name="opt2[]"   multiple  ><?php echo $opt2 ?></select>

								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-center pd-top25">電力管理</label>	
								<select class="selectpicker form-control col-sm-2  "  
								data-none-selected-text="請選擇" size="1"  name="opt3[]"   multiple  ><?php echo $opt3 ?></select>

							</div>
						</div>
						<?php
						}
						?>
<!--0420 後台權限複選 TEST END -->

<!--TEST-->

						<div class="form-group row  select-mar">
							<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">卡片權限</label>							
							<div class="col-sm-9 form-inline">
								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-center pd-top25">宿舍門禁</label>	
								<select class="selectpicker form-control col-sm-2  "  
								data-none-selected-text="請選擇" data-actions-box="true"   
								data-select-all-text='全選' data-deselect-all-text='取消全選' size="1"  name="opt4[]"   multiple  ><?php echo $opt4 ?></select>

								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-right pd-top25">電梯設置</label>	
								<select class="selectpicker form-control col-sm-2  "  
								data-none-selected-text="請選擇"   data-actions-box="true" 
								data-select-all-text='全選' data-deselect-all-text='取消全選'size="1"  name="opt5[]"   multiple  ><?php echo $opt5 ?></select>


								<!--
								<label for="exampleFormControlInput1" class="col-sm-2 col-form-label label-center">房號</label>	
								<select class="selectpicker form-control col-sm-2  "  
								data-none-selected-text="請選擇" data-live-search="true"
								data-select-all-text='全選' data-deselect-all-text='取消全選'  data-actions-box="true" size="1"  name="opt6[]"   multiple  ><?php echo $opt6 ?></select>
								-->
								
							</div>
						</div>
						<br>

						<div class="form-group row">
							<label for="exampleFormControlInput1"  class="col-sm-2 col-form-label label-right">備註</label>	
							<div class="col-sm-9">
								<input type="text" maxlength='30' class="form-control" id="exampleFormControlInput1" name="remark" placeholder="">
							</div>
						</div>
					  <br><br>
					  <button type="submit" class="btn  btn-loginfont btn-primary2  col-6 offset-3"><?php echo $lang->line("index.confirm_create");?></button>

					</form>

<br><br>


<!--TEST END-->


<!--

					<form action="memberUpd.php" method="post">  
					  <input type="hidden" name="act" value="新增">
					  <div class="form-group">
					    <label for="exampleFormControlInput1"><?php echo $lang->line("index.room_number");?></label>
					    <input required="required" type="text" class="form-control" name="room_strings" placeholder="Ex. 房號">
					  </div>
					  <div class="form-group"> 
					    <label for="exampleFormControlInput1">床位</label>
					    <input type="number" class="form-control" id="exampleFormControlInput1" name="berth_number" placeholder="ex. 床位" min="1" max="6">
					  </div>
					  <div class="form-group">
					    <label for="exampleFormControlInput1"><?php echo $lang->line("index.card_ID");?></label>
					    <input required="required"  maxlength="10" minlength="10" type="text" class="form-control" name="id_card" placeholder="Ex. 卡號(10碼)">
					  </div>
					  <div class="form-group">
					    <label for="exampleFormControlInput1"><?php echo $lang->line("index.student_ID");?></label>
					    <input required="required"  maxlength="9" minlength="9" type="text" class="form-control" name="username" placeholder="Ex. 學號">
					  </div>
					  <div class="form-group">
					    <label for="exampleFormControlInput1"><?php echo $lang->line("index.member_name");?></label>
					    <input type="text" class="form-control" id="exampleFormControlInput1" name="cname" placeholder="<?php echo $lang->line("index.please_enter_name"); ?>">
					  </div>
					  <div class="form-group">
					    <label for="exampleFormControlInput1">班級</label>
					    <input type="text" class="form-control" id="exampleFormControlInput1" name="user_class" placeholder="<?php echo $lang->line("index.please_enter_grade"); ?>">
					  </div>
					  <div class="form-group">
					    <label for="exampleFormControlInput1">預設金額</label>
					    <input type="text" class="form-control" name="balance" placeholder="預設是: 0" value="0">
					  </div> 
					  <button type="submit" class="btn btn-primary"><?php echo $lang->line("index.confirm_create");?></button>
					</form>
								-->
				</div>
			</div>
		</div>
	</div>
</section>
<?php // iframe('');?>
<?php include('footer_layout.php'); ?>