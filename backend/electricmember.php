<?php
    include('includes/header.php');
    include('includes/nav.php');
	include('includes/scripts.php');
	if(!$_SESSION['admin_user']['username']) alertMsg('請登入', '../session.php', true);
	
	$pagesize = 10; 
	$dong_arr  = array();
	$floor_arr = array();
	
	$add_sql    = '';
	$room_strings = $_GET['room_strings']; 
    $serach = $_GET['serach'];
	
	if(room_strings) {
		$add_sql .= " AND b.room_strings LIKE '%{$room_strings}%' ";
	}
 
  $sql = "
	SELECT COUNT(*) as 'count' 
	FROM  room_electric_situation a
	INNER JOIN `member` b ON a.member_id = b.id
	WHERE a.room_id<>'0' AND trim(b.room_strings) <> '' AND b.del_mark = 0 {$add_sql}
	ORDER BY a.room_id, b.room_strings, b.berth_number	
	";
	$stmt = $PDOLink->query($sql);
	$tmp = $stmt->fetch();                      
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
		$pageurl .= "<a href='?page=1&room_strings={$room_strings}&serach={$serach}'>".$lang->line("index.home").
					"</a> | <a href='?page={$prepage}&room_strings={$room_strings}&serach={$serach}'>".$lang->line("index.previous_page")."</a> | ";
	}

	if($page == $pagenum || $pagenum == 0) {     
		$pageurl .= " ".$lang->line("index.next_page")." | ".$lang->line("index.last_page")." ";
	} else {
		$pageurl .= "<a href='?page={$nextpage}&room_strings={$room_strings}&serach={$serach}'>".$lang->line("index.next_page").
					"</a> | <a href='?page={$pagenum}&room_strings={$room_strings}&serach={$serach}'>".$lang->line("index.last_page")."</a>";
	}

  $sql = "
	SELECT b.id AS 'member_id', b.username, b.id_card , b.room_strings, b.cname, b.identity, b.balance 
	FROM  room_electric_situation a
	INNER JOIN `member` b ON a.member_id = b.id
	WHERE a.room_id<>'0' AND trim(b.room_strings) <> '' AND b.del_mark = 0 {$add_sql}
	ORDER BY a.room_id, b.room_strings, b.berth_number 
	LIMIT ".($page-1) * $pagesize . ",".$pagesize;
	$stmt = $PDOLink->prepare($sql);
	$stmt->execute();
	$data = $stmt->fetchAll();
	
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">指定扣款設定</h1>

        <div class="row container-fluid mar-bot50">
        <?php if($_GET['success'] == 1){ ?>
            <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-success" role="alert">
            <strong>設定完成！</strong>
            </div>
        <?php } elseif($_GET['error'] == 1){ ?> 
            <div style="margin: 0 auto; text-align: center; width: 600px;" class="alert alert-danger" role="alert">
            <strong>設定失敗！</strong>
            </div>	
        <?php } ?>
        </div>  

        <!--指定扣款設定--->
        <div class="row">
            <div class="col-lg-12">
                <br>
                <!--Search-->
                <form id='mform2' action="" method="get" class='col-12'>
                      <div class="input-group mb-3">
                          <label for='exampleFormControlInput1' class=' col-sm-2 col-form-label label-right'>房號</label>
                          <input type='text' class='col-sm-8 form-control custom-select-lg' name='room_strings' placeholder='全部' />
                      </div>
                      <input type='hidden' name='serach' value='1'>
                      <button type='submit' class='btn  btnfont-30 btn-primary2 text-white col-lg-3 offset-lg-3'>查詢</button>
                      &nbsp;
                      <button type='button' class='btn  btnfont-30 btn-primary2 text-white col-lg-3' onclick='page_reset()'>重設</button>
                </form>
                <br>
                <!--Search END-->
                <div>
                    <!--Table-->
                    <p class="text-lg font-weight-bold NG-color">【說明】</p>
                    <p class="text-lg font-weight-bold NG-color">
                      1.指定控電：一鍵將房間控電權限，指派給對應的人<br>
                      2.硬體指令：指定房間控電權命令，id帶出member_id<br>
                      {"op":"PowerOn","table":"member","id":"95"}<br>
                      3. <button class="btn bg-gray-400 btn-circle" data-toggle='tooltip' data-placement='bottom' title="餘額不足無法控電" onclick="NosetElectricCtrl('<?php echo $member_id ; ?>')">
                          <i class="fas fa-bolt"></i>
                        </button>：餘額<=0，不可執行指定控電<br>
                      4.指定扣款初始化:將全部房間控電權恢復預設，指派給各房間有餘額的公用卡，<br>
                                      若公用卡餘額<=0，則控電權指派給有餘額的住宿生。<br>
                                      若公用卡餘額<=0，且房間無住宿生或無住宿生卡片中有錢，則整間房無法控電<br>
                    </p>
                    <div class="text-right">
                        <button onclick="setAllPubCard()" class="btn btnfont-30 text-white btn-warning my-4 col-auto">
                          <i class='fas fa-bolt'></i> 指定扣款初始化
                        </button>
                    </div>
                    <div class="table-responsive">
                                <table class="table  text-center font-weight-bold">
                                  <thead class="thead-green">
                                  <tr class="text-center">
                                    <th scope="col">指定控電</th>
                                    <th scope="col">房號</th>
                                    <th scope="col">姓名</th>									
                                    <th scope="col">學號</th>
                                    <th scope="col">卡號</th> 
                                    <!-- <th scope="col">餘額</th>  -->
                                  </tr>
                                  </thead> 
                                  <tbody> 
                                    <?php
                                      foreach($data as $row) {
                                        $member_id  = $row['member_id'];
                                        $username  = $row['username'];
                                        $id_card  = $row['id_card']; 
                                        $room_strings  = $row['room_strings'];
                                        $cname  = $row['cname'];
                                        $balance  = $row['balance'];
                                    ?>
                                    <tr>
                                      <td scope="row">
                                        <?php
                                        if($balance>0){
                                          echo"
                                            <button class='btn btn-warning btn-circle btn-lg2' data-toggle='tooltip' data-placement='bottom' title='指定控電' onclick='setElectricCtrl($member_id)'>
                                              <i class='fas fa-bolt'></i>
                                            </button>
                                          ";
                                        }else{
                                          echo"
                                          <button class='btn bg-gray-400 btn-circle btn-lg2' data-toggle='tooltip' data-placement='bottom' title='餘額不足無法控電' onclick='NosetElectricCtrl($member_id)'>
                                            <i class='fas fa-bolt'></i>
                                          </button>
                                          ";
                                        }?>
                                      </td>
                                      <td scope="row"><?php echo $room_strings; ?></td>
                                      <td scope="row"><?php echo $cname; ?></td>
                                      <td scope="row"><?php echo $username; ?></td>
                                      <td scope="row"><?php echo $id_card; ?></td>
                                      <!-- <td scope="row"><?php echo $balance; ?></td> -->
                                    </tr> 
                                    <?php	
                                      }
                                    ?>
                                    </tbody>
                                </table>
                    </div>
                    <!--Table END--->
					
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

            </div>
        </div>
        <!--指定扣款設定 END--->


    </div>
    <!-- /.container-fluid -->

<script>
function setAllPubCard(){
		if(confirm('初始化指定扣款，將取代原先控電人員並還原至預設\n是否繼續開通控電?')) {
      location.replace("model/electric_ctrl_pubcard.php");
	}
	return;
}

function setElectricCtrl(id){
		if(confirm('此動作將取代原先控電人員\n是否繼續開通控電?\n (1房間僅限1人有控電權)')) {
		location.replace("model/electric_ctrl_member.php?id=" + id);
	}
	return;
}
function NosetElectricCtrl(id){
  alert('餘額不足無法控電');
	return;
}
function page_reset() {
	location.replace('electricmember.php');
}
</script>

<?php
    include('includes/footer.php');
?>