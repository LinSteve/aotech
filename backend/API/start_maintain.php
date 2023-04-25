<?PHP
	include_once('../../config/db.php');

	$dong = $_POST['dong'];
    $floor = $_POST['floor'];
    $center_id = $_POST['center_id'];
    $Data['returnCode'] = "0000";

    try
    {
       $sql = "UPDATE `maintain` SET `update_time`=now(),`powerstaus` = 0,is_maintain=0,member_id=0 where room_id in (SELECT id FROM room where dong='".$dong."' and floor=".$floor." and center_id = ".$center_id.")";
       $sth = $PDOLink->exec($sql);

    }
    catch(Exception $e)
    {
        $Data['returnCode'] = "0001";
        $myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
        echo $myJSON;
        exit();
    }


    try
    {
        $sql = "
        UPDATE maintain as ma
        JOIN
        (SELECT s.member_id,s.room_id,s.powerstaus,r.mode FROM room_electric_situation  as s join room as r on room_id = r.id where dong='".$dong."' and floor=".$floor." and center_id = ".$center_id." and powerstaus=1  group by room_id
        UNION ALL
        SELECT a.member_id,a.room_id,a.powerstaus,a.mode FROM (SELECT s.member_id,s.room_id,s.powerstaus,r.mode, 
				(CASE 
                    WHEN COUNT(*) = COUNT(CASE WHEN powerstaus =0 THEN 1 ELSE NULL END) 
                    THEN 'true' 
                    ELSE 'false' 
                END) as all_match 
                FROM room_electric_situation  as s join room as r on room_id = r.id where dong='".$dong."' and floor=".$floor." and center_id =".$center_id."   group by room_id)  as a where  all_match = 'true') as s
                on ma.room_id = s.room_id 
        set ma.member_id = s.member_id,ma.powerstaus = s.powerstaus,ma.mode = s.mode,is_maintain=1
                ";
        $stmt = $PDOLink -> prepare($sql);
        $stmt -> execute(); 
    }
    catch(Exception $e)
    {
        $Data['returnCode'] = "0002";
        $myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
        echo $myJSON;
        exit();
    }

    try
    {
        $sql = "SELECT name,dong FROM host";
        $stmt = $PDOLink -> prepare($sql);
        $stmt -> execute(); 
        $result = $stmt->fetchAll();
    }
    catch(Exception $e)
    {
        $Data['returnCode'] = "0003";
        $myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
        echo $myJSON;
        exit();
    }

    $Computer_names_sql = "";
    $Run_Command_Computer_sql = "";
    foreach($result as $v)
    {
        $Computer_names_sql .= ",`".$v['name']."`";
        if ($dong == $v['dong'])
        {
            $Run_Command_Computer_sql .= ",'0'";
        }else{
            $Run_Command_Computer_sql .= ",'1'";
        }
    }

    try
    {
        $sql = "UPDATE `room` SET `mode` = '3' where dong='".$dong."' and center_id=".$center_id;
        $stmt = $PDOLink -> prepare($sql);
        $stmt -> execute(); 
    }
    catch(Exception $e)
    {
        $Data['returnCode'] = "0004";
        $myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
        echo $myJSON;
        exit();
    }


    try
    {  
        $sql = "INSERT INTO `system_setting` (`computer_name`, `c_code`,`add_date`) VALUES ('Web', \"".$sql."\",now())";
        $sth = $PDOLink->exec($sql);
    }
    catch(Exception $e)
    {
        $Data['returnCode'] = "0005";
        $myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
        echo $myJSON;
        exit();
    }


    try
    {
        
        $sql = "INSERT INTO `system_setting` (`computer_name`, `c_code`".$Computer_names_sql.") VALUES ('Web', '{\"op\":\"Layer_ChangeMode\",\"table\":\"room\",\"id\":\"".$center_id."\",\"field\":[\"mode\",\"price_degree\"]}'".$Run_Command_Computer_sql.")";
        $sth = $PDOLink->exec($sql);
    }
    catch(Exception $e)
    {
        $Data['returnCode'] = "0006";
        $myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
        echo $myJSON;
        exit();
    }

    try
    {  
        $sql = "UPDATE `room_electric_situation` SET `powerstaus` = 0 where room_id in (SELECT id FROM room where dong='".$dong."' and floor=".$floor." and center_id = ".$center_id.")";
        $sth = $PDOLink->exec($sql);
    }
    catch(Exception $e)
    {
        $Data['returnCode'] = "0007";
        $myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
        echo $myJSON;
        exit();
    }

    try
    {  
        $sql = "INSERT INTO `system_setting` (`computer_name`, `c_code`,`add_date`) VALUES ('Web', \"".$sql."\",now())";
        $sth = $PDOLink->exec($sql);
    }
    catch(Exception $e)
    {
        $Data['returnCode'] = "0008";
        $myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
        echo $myJSON;
        exit();
    }


$myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
echo $myJSON;
?>