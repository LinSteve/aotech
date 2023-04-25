<!-- nav.php -->

<header id='header'>
		<nav class='left'>
			<!--<a href='#menu'><span>Menu</span></a>-->
			<a href='#menu' class='for_mobile'><span class='glyphicon glyphicon-option-horizontal' style='font-size:42px'></span></a> 
		</nav>
		<!-- LOGO -->
		<a style='color: #fff;' href='index.php' class='logo'>
			
			<img class='school_image' src='img/logo.png'>  
			<!-- ".$lang->line("index.scllo_title")." -->
		</a>
		
<?php

	print " <nav class='right'>";
	        if ($_SESSION['user']['id']){
				//學生設定BTN
				print "	
				<ul class='navbar-nav ml-auto'>
					<!-- Nav Item - User Information -->
					<li class='nav-item dropdown no-arrow show'>
					  <a class='nav-link dropdown-toggle button  btn-orange' href='#' id='userDropdown' 
					  role='button'  data-toggle='dropdown' aria-haspopup='true' aria-expanded='true' 
					  style='padding: 0 1.75em; '>
					  <div class='mar-nav'>
						  <i class='fas fa-user fa-fw mr-2 text-white'></i>
						  <span class='mr-2 d-none d-lg-inline small'>設&nbsp;定</span>  
					  </div>
					  </a>
					  <!-- Dropdown - User Information -->
					  <div class='dropdown-menu dropdown-menu-right shadow' aria-labelledby='userDropdown' style='min-width:160px; line-height:3;' >
						<a class='dropdown-item text-green' href='admin_edit_student.php'>
						  <i class='fas fa-cog fa-sm fa-fw mr-2 '></i>
						  密碼變更
						</a>
						<div class='dropdown-divider'></div>
						<a class='dropdown-item  text-green' href='logout.php?data_type=admin'  data-target='#logoutModal'>
						  <i class='fas fa-sign-out-alt fa-sm fa-fw mr-2 '></i>
						  登出
						</a>
					  </div>
					</li>
				</ul>
			<!-下拉MENU END-->



			";
			   //OLD學生登出BTN
			   print "	
			   <!--<a href='logout.php?data_type=member' class='button alt'>
				   ".$lang->line("index.logout")."</a>-->";
			   
			//管理員設定BTN

	        } elseif ($_SESSION['admin_user']['id']) {
				print "
				<ul class='navbar-nav ml-auto'>
				<!-- Nav Item - User Information -->
				<li class='nav-item dropdown no-arrow show'>
				  <a class='nav-link dropdown-toggle button  btn-orange' href='#' id='userDropdown' 
				  role='button'  data-toggle='dropdown' aria-haspopup='true' aria-expanded='true' style='padding: 0 .5em; '>
				  	<div class='mar-nav'>
						<i class='fas fa-user fa-fw mr-2 text-white'></i>
						<span class='mr-2 d-none d-lg-inline small'>管理員設定</span>
				 	</div> 
				  </a>
				  <!-- Dropdown - User Information -->
				  <div class='dropdown-menu dropdown-menu-right shadow' aria-labelledby='userDropdown' style='min-width:160px; line-height:3;  top: 10px;' >
					<a class='dropdown-item text-green' href='admin_edit.php'>
					  <i class='fas fa-cog fa-sm fa-fw mr-2 '></i>
					  密碼變更
					</a>
					<div class='dropdown-divider'></div>
					<a class='dropdown-item  text-green' href='logout.php?data_type=admin'  data-target='#logoutModal'>
					  <i class='fas fa-sign-out-alt fa-sm fa-fw mr-2 '></i>
					  登出
					</a>
				  </div>
				</li>
			</ul>
		<!-下拉MENU END-->

		<!--OLD 登出BTN-->
			<!--<a href='logout.php?data_type=admin' class='button alt'>
					".$lang->line("index.logout")."</a>-->";
	        } else {
		       print "
				<a href='#' onclick=\"$('#identity2').show()\" class='button alt2 btnfont-21'>
				<div class='mar-nav'>".$lang->line("index.login")."</div>
				</a>

				<a href='#' onclick=\"$('#identity').show()\" class='button alt btnfont-21'>
					<div class='mar-nav'>".$lang->line("index.admin_login")."</div>
				</a>
				<!--<a href='login.php' class='button alt'>".$lang->line("index.login")."</a>  -->
				<!--<a href='admin_login.php' class='button alt'>".$lang->line("index.admin_login")."</a> -->
				";
			}
	print "	</nav>
	    <!-- <nav class='left'>
			<a style='margin: 0px 55px;' href='logout.php?data_type=member' class='button alt'>
				".$lang->line("index.logout")."
			</a>
		<nav> -->
	</header>
	<nav id='menu'> 
		<!--<a href='#menu'><span class='glyphicon glyphicon-option-horizontal' style='font-size:42px; margin-top:-30px; color: #066'></span></a>-->
		<ul class='links'>";
   	    	print "
				<li><a href='index.php'><span class='fas fa-home'></span>&nbsp;".$lang->line("index.home")."</a></li>
				<hr class='hr-style'>   
				<li><a href='content_us.php'><span class='fas fa-question-circle'></span>&nbsp;".$lang->line("index.customer_service")."</a></li>
				<hr class='hr-style'>
				<!-- <li><a href='illumination_member.php'>".$lang->line("index.outside_courts_inquiry_system")."</a></li>
				<li><a href='about.php'>".$lang->line("index.intelligent_school_introduction")."</a></li> -->";

			/* 學生權限 */
	        if ($_SESSION['user']['id']){

				print "
					 <li><a href='new-member2.php'><span class='fas fa-cog'></span>&nbsp;".$lang->line("index.student_center")."</a></li>
					 <hr class='hr-style'>
					 ";
			 } 
 


			 // 管理權限 
			if(isset($_SESSION['admin_user']['id'])) 
			{
				echo "
						<li><a href='new-member2.php'><span class='fas fa-cog'></span>&nbsp;".$lang->line("index.instructor_center")."</a></li>
						<hr class='hr-style'>
						";
					

				// if($_SESSION['admin_user']['id'] == 'aoadmin') {
					// print "<li><a href='system_administration.php'>最高系統管理</a></li>";
				// } else {
					
					// $sn  = $_SESSION['admin_user']['sn'];
					// $sql = 'SELECT * FROM menu_access WHERE `sn` = :sn';
					// $sth = $PDOLink->prepare($sql);
					// $sth->execute(array('sn' => $sn));
					// $result = $sth->fetch();
					
					// $access = $result['access'];
						
					// if($access != '') {
						// $sql = "SELECT * FROM `menu_list` WHERE `id` in ({$access})";
						
						// $sth = $PDOLink->prepare($sql);
						// $sth->execute(array());
						// $result = $sth->fetchAll();
						
						// foreach($result as $v) {
							// print "<li><a href=\"{$v['page']}\">".$lang->line("{$v['item_name']}")."</a></li>";	
						// }
					// }
				// }

				// print "<br>
						// <li><b style='color:#000000'>Language：</b>
							// <select onChange='location = this.options[this.selectedIndex].value;' style='color: #000;' name='' class=''>
								// <option value='#'>".$lang->line("index.language_select")."</option>
								// <option value='/ndhu/index.php?lang=zh-TW'>".$lang->line("index.chinese")."</option>
								// <option value='/ndhu/index.php?lang=en-us'>".$lang->line("index.english")."</option>
							// </select>
						// </li>";

			}	

				print "
				<li><a href='manual/東華-智慧校園管理系統網頁平台操作手冊（管理員).pdf' target='_blank'><span class='fas fa-arrow-circle-down'></span>&nbsp;".$lang->line("index.user_manual")."</a></li>
				<hr class='hr-style'>
				<li><a href='manual/東華-智慧校園管理系統網頁平台操作手冊（學生).pdf' target='_blank'><span class='fas fa-arrow-circle-down'></span>&nbsp;".$lang->line("index.student_manual")."</a></li>
				<hr class='hr-style'>
				<li><a href='manual/東華-智慧電力系統 主機及操作說明.pdf' target='_blank'><span class='fas fa-arrow-circle-down'></span>&nbsp;".$lang->line("index.top_up_machine_user_manual")."</a></li>
				</ul>";
				print "<ul class='actions vertical '>";
			//側選單登入適用手機板
	        if ($_SESSION['user']['id']){ 
		       print "
				".$_SESSION['user']['cname']." 登入中
				<li><a  href='admin_edit_student.php' class='button btn-orange col-12'>密碼變更</a></li>
				<li><a  href='logout.php?data_type=member' class='button btn-orange col-12'>登出</a></li>

				<!--<a  style='width: 200px;' href='logout.php?data_type=member' class='button alt'>登出</a>-->
				";
			} elseif ($_SESSION['admin_user']['id']) {

			   print "
				管理員 登入中
				<li><a  href='admin_edit.php' class='button btn-orange col-12'>密碼變更</a></li>
				<li><a  href='logout.php?data_type=admin' class='button btn-orange col-12'>登出</a></li>
				";
	        } else { 
		       print "
					<li><a href='#' onclick=\"$('#menu').prop('class', '');$('#identity').hide();$('#identity2').show()\" class='button alt2 col-12'>".$lang->line("index.login")."</a></li> 
					<li><a href='#' onclick=\"$('#menu').prop('class', '');$('#identity2').hide();$('#identity').show()\" class='button alt col-12'>".$lang->line("index.admin_login")."</a></li>
				";
			}

	print " </ul>
	</nav>";

?>