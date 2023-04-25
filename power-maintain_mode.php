<?php
    include('includes/header.php');
    include('includes/nav.php');
	  include('includes/scripts.php');
    
    # 棟別
    $sql = "SELECT DISTINCT a.dong, b.dong_name FROM `room` a INNER JOIN dongname b ON b.dong=a.dong";
    $dong_arr = func::excSQL($sql, $PDOLink, true);
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="mb-2 font-weight-bold">維護&脫離模式</h1>

        <!-- 製作說明 -->
        <div class="d-none w-100 mx-auto alert alert-orange text-dark">
          <h5>製作說明</h5>
          <dl>
            <dt>1.提示文字放置區域:</dt>
            <dd>維護模式=>表單送出成功後，顯示文字「已開啟維護模式！」</dd>
            <dd>脫離模式=>表單送出成功後，顯示文字「已關閉維護模式！」</dd>
            <hr>

            <dt>2.維護模式:</dt>
            <dd>【待確認】棟別：選項顯示「當前 是 脫離模式 」的棟別」</dd>
            <dd>【待確認】樓層：選項顯示，對應棟別，「當前 是 脫離模式 」的樓層(含選項全部)</dd>
            <dd>控制箱(center)：選項顯示，對應棟別&樓層，「當前 是 脫離模式 」的center_id(含選項全部)</dd>
            <dd>【待確認】硬體指令</dd>         
            <hr>

            <dt>3.脫離模式:</dt>
            <dd>【待確認】棟別：選項顯示「當前 是 維護模式 」的棟別」</dd>
            <dd>【待確認】樓層：選項顯示，對應棟別「當前 是 維護模式」的樓層(含選項全部)</dd>
            <dd>控制箱(center)：選項顯示，對應棟別&樓層，「當前 是 維護模式 」的center_id(含選項全部)</dd>
            <dd>【待確認】硬體指令</dd>         
            <hr>

            <dt>4.維護&脫離模式_表單AJAX數據交互:</dt>
                <dd>棟別初始預設：請選擇，此時棟別已有選項</dd>
                <dd>樓層初始預設：請選擇，此時選項只有請選擇，
                                  須選擇棟別後，才會跳出對應符合條件的選項。</dd>
                <dd>控制箱(center)初始預設：請選擇，此時選項只有請選擇，
                                  須選擇棟別&樓層後，才會跳出對應符合條件的選項。</dd>
                <dd>資料回傳：棟別代號、樓層數、center_id</dd>
                <dd>選項：選擇「全部」，value，回傳all</dd>  
                <dd>【待確認】硬體指令</dd>
            
            <hr>
            
            <dt>5.維護模式現況:只要有1個center是維護模式，則在下表中顯示對應棟別/樓層、更新時間</dt>
            <dd>#： 資料id</dd>
            <dd>當前模式：該棟該樓層當前維護檢修的狀態</dd>
            <dd>棟別/樓層：對應當前模式</dd>
            <dd>更新時間：此筆資料的更新時間</dd>
            <dd>資料排序：以資料更新時間，最新-舊排序</dd>
            
          </dl>
        </div>

        <!-- 提示文字放置區域 -->
        <div id="notice" class="fixed-top w-75 mx-auto"></div>  

        <!-- 維護/脫離設定頁 -->
        <div class="row justify-content-center">
            <!-- 維護模式 -->
            <div class="col-lg-6 mb-4">
              <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="mb-1 font-weight-bold text-center">
                          <h5 class='text-green'>維護模式</h5>
                          <hr>
                      </div>
						          <form id='form1' class='col-12'>
                          <div class="mb-3">
                              <label class="col col-form-label label-center">棟別</label>	
                                <select class="col form-control custom-select-lg"  size="1" name="dong" id="dong" required>
                                    <option value=''>請選擇</option>
                                    <?php
                                    foreach($dong_arr as $v) {
                                      echo "<option value='{$v['dong']}'>{$v['dong_name']}</option>";
                                    }
                                    ?>
                                </select>
                          </div>
                          <div class="mb-3">
                                <label class="col col-form-label label-center">樓層</label>	
                                <select class="col form-control custom-select-lg"  size="1" name="floor" id="floor" required></select>
											    </div>
                          <div class="mb-3">
                                <label class="col col-form-label label-center">控制箱(center)</label>	
                                <select class="col form-control custom-select-lg"  size="1" name="center" id="center" required>
                                  <option value="">請選擇</option>
                                </select>
											    </div>
                          <br>
                          <!-- <button type="submit" class="btn text-white btnfont-30 font-weight-bold  btn-info col-12"> -->
                          <button class="btn text-white btnfont-30 font-weight-bold  btn-info col-12">
                              <i class="fab fa-osi"></i>
                              開啟維護
                          </button>
						          </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- 維護模式 END -->

            <!-- 脫離模式 -->
            <div class="col-lg-6 mb-4">
              <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="mb-1 font-weight-bold text-center">
                          <h5 class='text-green'>脫離模式</h5>
                          <hr>
                      </div>
						          <form id='form2' action="" method="post" class='col-12'>
                          <div class="mb-3">
                              <label class="col col-form-label label-center">棟別</label>	
                                <select class="col form-control custom-select-lg"  size="1" name="dong" id="dong" required>
                                    <option value=''>請選擇</option>
                                    <?php
                                    foreach($dong_arr as $v) {
                                      echo "<option value='{$v['dong']}'>{$v['dong_name']}</option>";
                                    }
                                    ?>
                                </select>
                          </div>
                          <div class="mb-3">
                                <label class="col col-form-label label-center">樓層</label>	
                                <select class="col form-control custom-select-lg"  size="1" name="floor" id="floor" required></select>
											    </div>
                          <div class="mb-3">
                                <label class="col col-form-label label-center">控制箱(center)</label>	
                                <select class="col form-control custom-select-lg"  size="1" name="center" id="center" required>
                                  <option value="">請選擇</option>
                                </select>
											    </div>
                          <br>

                          <button type="submit" class="btn text-white btnfont-30 font-weight-bold  btn-success col-12">
                              <i class="fab fa-osi"></i>
                              關閉維護
                          </button>
						          </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- 脫離模式 END -->
        </div>
        <!-- 維護/脫離設定頁 END-->

        <!-- 維護模式現況 -->
        <div class="row">
            <div class="col-12 mt-4">
                <h2 class="text-primary font-weight-bold border-bottom-primary">維護模式現況</h2>
            </div>
                <div class="col-12 table-responsive">
                    <table class="table text-center font-weight-bold">
                      <thead class="thead-green">
                        <th>id</th>
                        <th>當前模式</th>
                        <th>棟別/樓層</th>
                        <th>更新時間</th>
                      </thead>
                      <tbody class="text-orange"></tbody>
                    </table>
                </div>
        </div>
        <!-- 維護模式現況 END -->
    </div>
<style>
  #notice{
    top: 50%;
    bottom: 50%;
  }
</style>
<script>
  /* maintain_mode_維護/脫離模式Obj調用說明
    form_dong
      作用：存放form id
    ------------------------------------------------
    form_element()
      作用：抓取form id，創建棟別、樓層、控制箱欄位id
    ------------------------------------------------
    floor_url
      作用：存放樓層選項 URL
    query_url
      作用：存放控制箱選項、維護模式現況 API  URL
    post_url
      作用：存放表單提交POST URL
    ------------------------------------------------
    data_empty(element)
      作用：清空該元素內的資料
    ------------------------------------------------
    ajax_floor(dong_id,floor_id,url)
      作用：依據棟別，顯示對應的樓層
      dong_id     棟別欄位id
      floor_id    樓層欄位id
      url         樓層API
    ------------------------------------------------
    ajax_center:(dong_id,floor_id,center_id,url)
      作用：依據樓層，顯示對應的center_id
      dong_id     棟別欄位id
      floor_id    樓層欄位id
      center_id   控制箱欄位center_id
      url         樓層API
    ------------------------------------------------
    ajax_post(url,boolean_ctrl)
      作用：表單POST提交
      this    綁定當前表單，作為該功能的this
      url     POST URL
      boolean true顯示 開啟、false顯示 關閉，控制提示文字的顯示內容。
    ------------------------------------------------
    circle_data(arr)
      作用：表單element初始化，每3個執行一次
      arr => 存放本頁表單element id
    ------------------------------------------------
    change_floor(index1,index2)
      作用：維護、脫離表單，顯示當前可選的樓層
      index1  棟別欄位id
      index2  樓層欄位id
    ------------------------------------------------
    change_center(index1,index2,index3,url)
      作用：維護、脫離表單，顯示當前可選的center_id
      index1  棟別欄位id
      index2  樓層欄位id
      index3  控制箱欄位id
      url     option顯示連結
    ------------------------------------------------
  
  */
  const maintain_mode = {
    form_dong:['#form1','#form2'],
    form_element:function(){
      const data=[];
      $.each(this.form_dong,(i,v)=>{
        data.push(`${v} #dong`,`${v} #floor`,`${v} #center`);
      });
      return data;
    },
    floor_url:'model/ajax_floor_init_list.php',
    query_url:['./API/query_maintained_center_id.php',
              './API/query_maintaining_center_id.php',
              './API/query_maintaining.php'],
    post_url:['./API/start_maintain.php',
              './API/end_maintain.php'],
    data_empty:(element)=>{$(element).empty();},   
    ajax_floor:(dong_id,floor_id,url)=>{
          let dong_val = $(`${dong_id}`).val();
          if(dong_val != '') {
            $.ajax({
              url,
              data: {dong: dong_val},
              type: 'POST',
              success: function(data) {
                $(floor_id).html(data);
                let floor_val = $(`${floor_id}`).val();
                if(floor_val=== 'all' ){ $(`${floor_id} option:eq(0)`).text('請選擇');}
              }
            });
          } else {
            $(floor_id).html("<option value=''>請選擇</option>");
          }
    },
    ajax_center:(dong_id,floor_id,center_id,url)=>{
          let dong_val = $(dong_id).val();
          let floor_val = $(floor_id).val();
          if(dong_val != '' && floor_val != '') {
            $.ajax({
              url,
              data: `dong=${dong_val}&floor=${floor_val}`,
              dataType:'json',
              type: 'GET',
              success: function(data) {
                  maintain_mode.data_empty(center_id);
                  $(center_id).append("<option value=''>請選擇</option>");
                  $.each(data,(i,v)=>{
                    $(center_id).append(`<option value='${v}'>${v}</option>`);
                  });
              }
            });
          } else {
            $(center_id).html("<option value=''>請選擇</option>");
          }
    },
    ajax_get:(url)=>{
      const data = $(this).serializeArray();
      $.ajax({
        type:'get',
        url,
        dataType:'json',
      }).done((res)=>{
            maintain_mode.data_empty('tbody');
            // 0會自動走向false路線，所以要雙重否定
            let isreCode  = !res.returnCode;
            // 以資料更新時間，最新-舊排序
            const data  = res.data.sort(function(a,b){return a.update_time < b.update_time ? 1:-1} );
            // const data  = res.data.sort(function(a,b){return a.id > b.id ? 1:-1} );
            if(isreCode){
                $.each(data,(i,v)=>{
                  let id = v.id,
                      now_mode = '維護中',
                      location = v.location,
                      center = v.center_id,
                      update_time = v.update_time,
                      location_center =`${location}<br>center:${center}`;
                  $('tbody').append(`
                      <tr>
                        <td>${id}</td>
                        <td>${now_mode}</td>
                        <td>${location}</td>
                        <!-- <td>${location_center}</td> -->
                        <td>${update_time}</td>
                      </tr>
                  `)
                })
            }else{
              alert('現況抓不到資料，詳洽硬體工程師!')
            }

        })
        // .fail((err,exception)=>{alert('連線失敗，抓不到資料'+err.status);console.log('連線失敗，抓不到資料'+err.status,exception);})
        .fail((err)=>{
          alert(`連線失敗，抓不到資料\n
            當前狀態：${err.readyState}\n
            HTTP狀態代碼:${err.status}\n
            HTTP狀態代碼訊息:${err.statusText}\n
            服務器返回訊息:${err.responseText}
          `);
          console.log(`連線失敗，抓不到資料\n
            當前狀態：${err.readyState}\n
            HTTP狀態代碼:${err.status}\n
            HTTP狀態代碼訊息:${err.statusText}\n
            服務器返回訊息:${err.responseText}
          `);})
    },
    ajax_post:function(url,boolean_ctrl){
      if(confirm('是否確認送出?')) {
        const data = $(this).serializeArray();
          $.ajax({
            type:'post',
            url,
            data:`dong=${data[0].value}&floor=${data[1].value}&center_id=${data[2].value}`,
            dataType:'json',
          }).done((data)=>{
              let notice = $('#notice');
              let return_text = boolean_ctrl ? '開啟' : '關閉';
              let msg = (data.returnCode == 0) ? `成功${return_text}維護模式！` : `error：${data.returnCode}`;
              let msg_bg = (data.returnCode == 0) ? 'alert-success' : 'alert-danger';
              let notice_text = `
                    <div class="alert ${msg_bg} text-center alert-dismissible fade show" role="alert">
                      <strong>${msg}</strong>
                    </div>`;
              notice.append(notice_text);
            })
            .fail((err)=>{alert('連線失敗請重新再試!!');})
            .always(maintain_mode.alert_func())
      }
      return;
    },
    alert_func:()=>{
      let i = 0;
      const from1 = arr.slice(0,3);
      const from2 = arr.slice(3,6);
      maintain_mode.ajax_center(from1[i],from1[i+1],from1[i+2],query_url[0]);
      maintain_mode.ajax_center(from2[i],from2[i+1],from2[i+2],query_url[1]);
      setTimeout(()=>{
        $('.alert').removeClass('show');
        maintain_mode.data_empty('#notice');
      },1500);
      maintain_mode.ajax_get(query_url[2]);
    },
    circle_data:function(arr){
        for(let i=0,n=3, len=arr.length; i<len; i=i+n){
          this.ajax_floor(arr[i],arr[i+1],this.floor_url);
        }
    },
    change_floor:function(index1,index2){
          $(index1).change(()=>{
              this.ajax_floor(index1,index2,this.floor_url);
          });
    },
    change_center:function(index1,index2,index3,url){
      $(index2).change(()=>{
          this.ajax_center(index1,index2,index3,url);
      });
    }
  }

// 開始調用
  // 初始預設&數據
    const arr =  maintain_mode.form_element();
    const query_url =  maintain_mode.query_url;
    const post_url =  maintain_mode.post_url;

    $(document).ready(function() {
      maintain_mode.circle_data(arr);
      maintain_mode.ajax_get(query_url[2]);
    });

  // 維護、脫離表單_對應棟別，顯示當前對應樓層
    maintain_mode.change_floor(arr[0],arr[1]);
    maintain_mode.change_floor(arr[3],arr[4]);

  // 維護、脫離表單_對應棟別&樓層，顯示當前可選的center_id
    maintain_mode.change_center(arr[0],arr[1],arr[2],query_url[0]);
    maintain_mode.change_center(arr[3],arr[4],arr[5],query_url[1]);

  // 表單提交
    $('#form1').on('submit',function(e){
      e.preventDefault();
      maintain_mode.ajax_post.call(this,post_url[0],true);
    });
    $('#form2').on('submit',function(e){
      e.preventDefault();
      maintain_mode.ajax_post.call(this,post_url[1],false);
    });

</script>

<?php
    include('includes/footer.php');
?>