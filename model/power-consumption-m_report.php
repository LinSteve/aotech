<?php include_once("../config/db.php"); ?>
<?php require_once '../vendor/autoload.php'; ?>
<?php
    set_time_limit(0);
	
    $admin = $_SESSION['admin_user']['id'];
    if($admin) {
        $dong = trim($_GET['dong']);
        $year_st = trim($_GET['year_st']);
        $month_st = trim($_GET['month_st']);
        $search   = trim($_GET['search']);
        $year_end = trim($_GET['year_st']);
        # 結束月取隔月一號的第一筆資料
        $st_time = $year_st.'-'.$month_st.'-01 00:00:00';
        $month_end = date( "m", strtotime( $st_time." +1 month" )); 
        if($month_st == '12') {// 如果12月 ,結束年月為隔年1月的第一筆資料
            $year_end = $year_st + 1;
        }
        $total = $_GET['total']; //棟別名稱+度數
        if ($dong && $year_st && $month_st) {
            $filename  = "棟別月用電總計.csv";
            $body_date = "列印日期:" . date("Ymd") . "棟別月用電總計";    
            $body_head = array("房號", "開始時間", "結束時間", "用電總計");
            $body_tail = array("經手人", "", "主辦出納", "", "主辦會計", "", "機關長官", "");
            $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
            $xls = $spreadsheet->getActiveSheet();
            $param_st = array(
                ":year_st"=> $year_st,
                ":month_st"=> $month_st."-01 00:00:00",
                ":year_end"=> $year_end,
                ":month_end1"=> $month_end."-01 00:00:59", 
                ":dong" => $dong 
            ); 
            $sql = "select 
            (select name from room where id = a.room_id) as name,a.amonut as start_amonut,b.amonut as end_amonut,
            ROUND((b.amonut-a.amonut),2) as use_amonut,DATE_FORMAT(a.update_date,'%Y-%m-%d %H:%i:%s') as start_time,
            DATE_FORMAT(b.update_date,'%Y-%m-%d %H:%i:%s') as end_time
            from  room_amonut_log as a
            INNER JOIN (select room_id,max(b.amonut) as amonut,max(b.update_date) as update_date 
                                    from  room_amonut_log as b where b.update_date >= '".$param_st[':year_st']."-".$param_st[':month_st']."' and b.update_date <=  '".$param_st[':year_end']."-".$param_st[':month_end1']."' group by b.room_id) as b on a.room_id = b.room_id
            where a.update_date >= '".$param_st[':year_st']."-".$param_st[':month_st']."' and a.update_date <= '".$param_st[':year_end']."-".$param_st[':month_end1']."' and a.room_id in (SELECT id FROM room where dong = '".$param_st[':dong']."')
            group by a.room_id order by a.room_id ";  
            $room_data  = func::excSQL($sql, $PDOLink, true);

            foreach ($room_data as $k => $row) { 
                $room_name = $row['name']; // 房號
                $room_ele = $row['use_amonut']; // 單間房的總度數
                $st_time = $row['start_time'];
                $ed_time = $row['end_time'];
                if ($k == 0) 
                {
                    $xls->setCellValueByColumnAndRow(1, $k + 1, $body_date); 
                    $xls->setCellValueByColumnAndRow(1, $k + 2, $total);
                    foreach ($body_head as $i => $v) {
                        $xls->setCellValueByColumnAndRow($i + 1, $k + 3, $v);
                    }
                } 
                $i = 1; 
                $xls->setCellValueByColumnAndRow($i++, $k + 4, $room_name);				
                $xls->setCellValueByColumnAndRow($i++, $k + 4, $st_time ); 
                $xls->setCellValueByColumnAndRow($i++, $k + 4, $ed_time); 
                $xls->setCellValueByColumnAndRow($i++, $k + 4, $room_ele);
            }

            foreach ($body_tail as $i => $v) {
                $xls->setCellValueByColumnAndRow($i + 1, $k + 6, $v);
            }

            header('Content-Type: text/csv;charset=UTF-8');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Csv');
            $objWriter->setUseBOM(true);
            $objWriter->save('php://output');
            $room_data = null;
            $body_tail = null;
            $body_head = null;
        }else {
            die(header("location: ../power-consumption-m.php?error=6&dong="+dong+"&year_st="+year_st+"&month_st="+month_st));
        }  
    }else{
        func::alertMsg('請登入', '../index.php' , true);
        exit();
    }
        
?>