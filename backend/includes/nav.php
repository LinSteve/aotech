<?php 
	include('chk_log_in.php'); 
?>

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-darkblue sidebar sidebar-dark accordion toggled" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon">
          <!--<i class="fas fa-home"></i>-->
          <img src='img/logo2.png' class='h-auto w-100'>
        </div>
        <!--<div class="sidebar-brand-text mx-3"><sup></sup></div>-->
      </a>


      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>


      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="index.php">
          <i class="fas fa-fw fa-home"></i>
          <span>總覽</span>
        </a>
      </li>




      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">
      <!-- Heading -->
      <div class="sidebar-heading">
        前台功能
      </div>

      <!-- 客服中心 -->
      <li class="nav-item">
        <a class="nav-link" href="content_us.php">
          <i class="fas fa-fw fa-question-circle"></i>
          <span>客服中心</span>
        </a>
      </li>

      <!-- 餘額查詢-->
      <li class="nav-item">
        <a class="nav-link" href="balancesearch.php">
          <i class="fas fa-dollar-sign"></i>
          <span>餘額查詢</span>
        </a>
      </li>

      <!-- Nav Item - 宿舍名單群組管理 -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          <i class="fas fa-fw fa-user"></i>
          <span>宿舍名單群組管理</span>
        </a>
        <div id="collapseOne" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="editmember.php">修改名單資料</a>
            <a class="collapse-item" href="electricmember.php">指定扣款設定</a>
          </div>
        </div>
      </li>



      
      <!-- Nav Item - 智慧電力管理 -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
          <i class="fas fa-fw fa-charging-station"></i>
          <span>智慧空調管理</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="power-record.php">電力使用紀錄</a>
            <a class="collapse-item" href="power-storedvalue.php">學生儲值紀錄</a>
            <a class="collapse-item" href="power-nowsystem.php">系統使用現況</a>
            <a class="collapse-item" href="power-room_initialize.php">房間初始化</a>
            <!--<a class="collapse-item" href="">房門休眠燈設定</a>-->
            <a class="collapse-item" href="power-errormessage.php">房間錯誤資訊</a>
            <a class="collapse-item" href="power-ratecharge.php">費率 & 收費模式</a>
            <!--<a class="collapse-item" href="power-news.php">通知設定</a>-->
          </div>
        </div>
      </li>

<!-- Nav Item - 智慧研習室管理
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSix" aria-expanded="true" aria-controls="collapseSix">
          <i class="fas fa-book-reader"></i>
          <span>智慧研習室<br>管理</span>
        </a>
        <div id="collapseSix" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="study-record.php">電力使用紀錄</a>
            <a class="collapse-item" href="study-storedvalue.php">研習卡儲值紀錄</a>
            <a class="collapse-item" href="study-nowsystem.php">系統使用現況</a>
            <a class="collapse-item" href="study-moderate.php">模式設定</a>
          </div>
        </div>
      </li>
 -->


      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">
      <!-- Heading -->
      <div class="sidebar-heading">
        後台功能
      </div>

      <!-- Nav Item - Log紀錄 -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
          <i class="fas fa-fw fa-clipboard-list"></i>
          <span>Log紀錄</span>
        </a>
        <div id="collapseThree" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="log-frontdesk.php">Web Log</a>
            <a class="collapse-item" href="log-sql.php">SQL Log</a>
            <a class="collapse-item" href="log-initialize.php">初始化 Log</a>
          </div>
        </div>
      </li>

      
      <!-- Nav Item - 後台開發設定 -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
          <i class="fas fa-fw fa-cog"></i>
          <span>後台開發設定</span>
        </a>
        <div id="collapseFour" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="develop-kiosk.php">工程模式設定</a>
            <a class="collapse-item" href="develop-mysql.php">網路檢測 & SQL連接數</a>
            <a class="collapse-item" href="develop-testcard.php">測試階段功能</a>
          </div>
        </div>
      </li>





      <!-- Nav Item - 系統現況 -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
          <i class="fas fa-fw fa-database"></i>
          <span>系統現況</span>
        </a>
        <div id="collapseFive" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="system-hardwarelist.php">宿舍硬體系統檢測</a>
            <a class="collapse-item" href="system-reset.php">硬體系統重啟</a>
            <a class="collapse-item" href="curfew-door_initialize.php">卡機重啟</a>
            <a class="collapse-item" href="system-meterlist.php">系統檢測通訊狀況</a>
          </div>
        </div>
      </li>


    </ul>
    <!-- End of Sidebar -->





    <!-- Content Wrapper head-->
    <div id="content-wrapper" class="d-flex flex-column">
    <!-- Main Content -->
    <div id="content">
        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-gray topbar mb-4 static-top">

          <!-- Sidebar Toggle (Topbar) phone nav icon-->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>


          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">

            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">AO 管理員</span>
                <img class="img-profile rounded-circle" src="img/logo.png">
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <!--後端登出功能
                <a class="dropdown-item" href="logout.php?data_type=admin" data-target="#logoutModal">
                -->
                <a class="dropdown-item" href="logout.php" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-700"></i>
                  登出
                </a>
              </div>
            </li>

          </ul>

        </nav>
        <!-- End of Topbar -->



    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>




