<?php
    include('includes/header.php');
    include('includes/nav.php');
	
	$pagesize = 10;

	$sql = "SELECT count(*) as 'count' FROM system_setting";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$tmp = $rs->fetch();
	
	$sql = "SELECT * FROM information_schema.PROCESSLIST";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$data = $rs->fetchAll();
	
	$sync_num = $tmp['count'];
	$conn_num = sizeof($data);
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">網路檢測&MySQL連接數</h1>
        <!--
        <p class="text-lg text-center font-weight-bold NG-color">
          參照東華網路檢測與MySQL連接數。
        </p>
        -->
        <!-- Content Row -->
        <div class="row">

            <!-- 目前MySQL連接數 -->
            <div class="col-xl-6 col-md-6 mb-4">
              <div class="card border-bottom-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="mb-1 font-weight-bold text-gray-800 text-center">
                        <h5 class='text-green'>目前MySQL連接數</h5>
                        <hr>
                        <div class="alert alert-success mb-0">
                            <h4 class="mb-0 p-3 font-weight-bold"><?php echo $conn_num ?> /Link</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- 目前MySQL連接數 END -->

            <!-- 目前資料同步更新數量 -->
            <div class="col-xl-6 col-md-6 mb-4">
              <div class="card border-bottom-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="mb-1 font-weight-bold text-gray-800 text-center">
                        <h5 class='text-green'>目前資料同步更新數量</h5>
                        <hr>
                        <div class="alert alert-info mb-0">
                            <h4 class="mb-0 p-3 font-weight-bold"><?php echo $sync_num ?> /Data</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- 目前資料同步更新數量 END -->




            <!-- Table Card-->
            <div class="col-xl-12 col-md-12 mb-4">
              <div class="card shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="h5 mb-0 font-weight-bold text-gray-800">

                        <!--Table--->
                        <div class="table-responsive">
                                      <table class="table  text-center font-weight-bold">
                                        <thead class="thead-green">
                                        <tr class="text-center">
                                          <th scope="col">ID</th>
                                          <th scope="col">USER</th>
                                          <th scope="col">TIME</th>
                                          <th scope="col">DB</th>
                                          <th scope="col">HOST</th>
                                        </tr>
                                        </thead>
                                        <tbody class='varlist'>
										<?php
										foreach($data as $v) {
										?>
                                          <tr>
                                            <td><?php echo $v['ID'] ?></td>
                                            <td><?php echo $v['USER'] ?></td>
                                            <td><?php echo $v['TIME'] ?></td>
                                            <td><?php echo $v['DB'] ?></td>
                                            <td><?php echo $v['HOST'] ?></td>
                                          </tr>
										<?php
										}
										?>
										                    <!--
                                          <tr>
                                            <td>36783223</td>
                                            <td>andy</td>
                                            <td>0</td>
                                            <td>ndhu_db</td>
                                            <td>localhost</td>
                                          </tr>
                                          -->
                                          </tbody>
                                      </table>
                        </div>                        
                        


                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>




        </div>
        <!-- Content Row -->

    </div>
    <!-- /.container-fluid -->

<!--checkbox-->

<?php
    include('includes/scripts.php');
    include('includes/footer.php');
?>