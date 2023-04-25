<?php
	include_once("../config/db.php");

    $sql = "SELECT b.NAME AS name FROM member a LEFT JOIN room b ON b.NAME != a.room_strings
            WHERE a.room_strings='' GROUP BY b.`name`";
    //echo 'sql:'.$sql.'<BR>';
    $rs  = $PDOLink->prepare($sql);
    $rs->execute();
    $data = $rs->fetchAll();
    //print_r($data);    
    //if($data) {
        //echo $v['name'].'<BR>';
        foreach($data as $v) {
            //echo $v['name'].'<BR>';
            $sql2 = "SELECT * FROM room WHERE name='".$v['name']."'";
            //echo 'sql2:'.$sql2.'<BR>';
            $rs2  = $PDOLink->prepare($sql2);
            $rs2->execute();
            $data2 = $rs2->fetchAll();
            if(!$data2) echo "d:".$v['name'].'<BR>';
        }
    //}
?>
