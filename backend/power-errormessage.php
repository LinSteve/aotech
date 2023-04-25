<?php
    include('includes/header.php');
    include('includes/nav.php');
	
	$pagesize = 10;
	
	$sql = "SELECT COUNT(*) as 'count' FROM hardwareerrormessage";
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
	
	$sql = "SELECT * FROM `hardwareerrormessage` ORDER BY id DESC LIMIT ".($page-1)* $pagesize . ",".$pagesize;
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
?>

<!-- Begin Page Content -->
<div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">房間錯誤資訊</h1>
        <!--<p class="text-lg text-center font-weight-bold NG-color">
          比照東華房間錯誤資訊製作
		</p>
		-->
        <!--Table--->
        <div class="table-responsive">
                      <table class="table  text-center font-weight-bold">
                        <thead class="thead-green">
                          <tr class="text-center">
                            <th scope="col">Date Time</th>
                            <th scope="col">Error Message</th>
                            <th scope="col">Mode</th>
                            <th scope="col">Room</th>
                            <th scope="col">AmountLet</th>
                            <th scope="col">AmountCut</th>
                            <th scope="col">AmountRight</th>
                          </tr>
                        </thead>
                        <tbody class='error-message'>
						<?php						
						foreach($data as $v) {
						?>
                          <tr>
                            <td><?php echo $v['add_date']?></td>
                            <td><?php echo $v['ErrorMessage']?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
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


<?php
    include('includes/scripts.php');
    include('includes/footer.php');
?>