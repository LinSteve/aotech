<!-- FooterTEST -->
<footer id="footer" class="sticky-footer footer-bg">
<div class="copyright">
<?php print " &copy; AOTECH合創數位科技2022"//.$lang->line("index.browser_suggested_size").": 1280 * 900為佳<br> "; ?>
<?php if(false && !isset($_SESSION['admin_user']['sn']) && !isset($_SESSION['user']['sn'])) { ?>
	<a href="admin_login.php"><?php Echo $lang->line("index.admin_login"); ?></a>.
<?php } ?>
</div>
</footer>		
	
<!-- </section> -->
<script src="assets/js/jquery.scrolly.min.js"></script>
<script src="assets/js/skel.min.js"></script>
<script src="assets/js/util.js"></script>
<script src="assets/js/main.js"></script>

<script>//icon提示文字效果
	$(function () { $("[data-toggle='tooltip']").tooltip();});
</script>

    <script>
        $(function(){
            function footerPosition(){
                var contentHeight = document.body.scrollHeight;//網頁正文全文高度
                var winHeight = window.innerHeight;//可視窗口高度，不包括瀏覽器頂部工具欄
                if(!(contentHeight > winHeight)){
                    //當網頁正文高度小於可視窗口高度時，為footer添加類fixed-bottom
                    $('#footer').css({'position' : 'fixed'});
                } else {
                    $('#footer').css({'position' : 'static'});
                }
            }
            footerPosition();
            $(window).resize(footerPosition);
        });
    </script>
</body>
</html>



