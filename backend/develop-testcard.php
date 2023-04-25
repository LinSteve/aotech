<?php
    include('includes/header.php');
    include('includes/nav.php');
?>

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="mb-2 font-weight-bold">測試階段功能</h1>

          <!-- Content Row -->
          <div class="row justify-content-center">
            <div class="w-100 alert alert-info text-center">
              以下收納現場實際測試時，會用到的功能，當然不一定每個專案都會用到！
            </div>
            <div id="wait_data" class="text-lg col-12 mt-5 d-none">
              <div class="text-center text-white">
                <div class="spinner-border" role="status"></div>
                <p class="mb-0">處理中</p>
              </div>
              <style>
                #wait_data{
                  position: fixed;
                  z-index:3;
                  font-weight:800;
                  background:#1e283eba;
                }
              </style>
            </div>
            <div class="col-xl-3 col-md-6 mb-4 shadow alert alert-light text-center">
                <h5 class="text-green text-info border-bottom-info">全部測試卡建立</h5>
                <form id='form1'>
                  <div class='input-group-lg'>
                    <label class='col-form-label label-left col'>輸入卡號(純數字)</label>
                    <input required type='text' class='form-control' name="idcard" minlength="1" maxlength="10" value="<?php echo $idcard; ?>">
                  </div>
                  <button type='button' id='btn1' class='btn btnfont-30 text-white btn-info my-4 col-auto'>新增</button>
                </form>
                <div id='results' class="text-left"></div>
            </div>
          </div>
          <!-- Content Row -->


        </div>
        <!-- /.container-fluid -->

<?php
    include('includes/scripts.php');
    include('includes/footer.php');
?>
<script>
  $('#btn1').on('click', function () {
    Test("#form1",10);
  });
  function Test(id,length){
    var Form_data = $(id).serializeArray();
    // 卡號輸入移除值內所有空格
    var idcard=Form_data[0].value.replace(/\s*/g,'');

    // 判斷卡號值不能是空白或0，且長度要小於等於10
    if(idcard!='' && idcard!=0 && idcard.length<=10){
      alert('卡號不是空值，且輸入長度小於10');
      // 不足10碼前面自動補0
      // while(idcard.length<10) {idcard="0"+idcard}
      var idcard = String(idcard);
      var length = length - idcard.length;
      var idcard =('0'.repeat(length) + idcard);
      // 開始處理卡號回傳
        $.ajax({
            url:'VersionConversion/conversion.php',
            type:"GET",
            data:{idcard:idcard},	
            dataType:'html',
            beforeSend:function(){
              $('#wait_data').removeClass("d-none");
            },
            success:function($idcard){
              $("#results").append("<h6 class='text-dark'>新增測試卡號:" + $idcard +"</h6>");
            },
            complete:function(){
              $('#wait_data').addClass("d-none");
            },
            error: function(err) {
              alert('連線失敗');
              return false;
            }
          });
    }else{
      alert('建立失敗：卡號可能有空值或長度為0'); 
      return false;
    }
  }
</script>