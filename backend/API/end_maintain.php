<?PHP
	include_once('../../config/db.php');

	$dong = $_POST['dong'];
    $floor = $_POST['floor'];
    $center_id = $_POST['center_id'];
    $Data['returnCode'] = "0000";

    try
    {
        $sql = "SELECT name,dong FROM host";
        $stmt = $PDOLink -> prepare($sql);
        $stmt -> execute(); 
        $result = $stmt->fetchAll();
    }
    catch(Exception $e)
    {
        $Data['returnCode'] = "0001";
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
        $sql ="SELECT * FROM maintain where room_id in (SELECT id FROM room where dong='".$dong."' and floor=".$floor." and center_id = ".$center_id.")";
        $stmt = $PDOLink -> prepare($sql);
        $stmt -> execute(); 
        $result = $stmt->fetchAll();
    }
    catch(Exception $e)
    {
        $Data['returnCode'] = "0002";
        $myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
        echo $myJSON;
        exit();
    }


    foreach($result as $v)
    {
        if($v['mode'] !=3)
        {
            $sql = "UPDATE `room` SET `mode` = ".$v['mode']." WHERE id=".$v['room_id'];
            $sth = $PDOLink->exec($sql);
            insert_sql($sql,$PDOLink);

            if ($v['powerstaus'] == 1)
            {
                $sql = "UPDATE `room_electric_situation` SET `powerstaus` = '1' WHERE member_id = ".$v['member_id'];
                $sth = $PDOLink->exec($sql);
                insert_sql($sql,$PDOLink);
                try
                {
                    
                    $sql = "INSERT INTO `system_setting` (`computer_name`, `c_code`".$Computer_names_sql.") VALUES ('Web', '{\"op\":\"Single_Initialize\",\"table\":\"room\",\"id\":\"".$v['room_id']."\"}'".$Run_Command_Computer_sql.")";
                    $sth = $PDOLink->exec($sql);
                }
                catch(Exception $e)
                {
                    $Data['returnCode'] = "0003";
                    $myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
                    echo $myJSON;
                    exit();
                }
            }else{
                try
                {
                    
                    $sql = "INSERT INTO `system_setting` (`computer_name`, `c_code`".$Computer_names_sql.") VALUES ('Web', '{\"op\":\"Single_ChangeMode\",\"table\":\"room\",\"id\":\"".$v['room_id']."\",\"field\":[\"mode\",\"price_degree\"]}'".$Run_Command_Computer_sql.")";
                    $sth = $PDOLink->exec($sql);
                }
                catch(Exception $e)
                {
                    $Data['returnCode'] = "0004";
                    $myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
                    echo $myJSON;
                    exit();
                }
            }
        }
    }
   

    try
    {
       $sql = "UPDATE `maintain` SET `update_time`=now(),`powerstaus` = 0,is_maintain=0,member_id=0 where room_id in (SELECT id FROM room where dong='".$dong."' and floor=".$floor." and center_id = ".$center_id.")";
       $sth = $PDOLink->exec($sql);

    }
    catch(Exception $e)
    {
        $Data['returnCode'] = "0005";
        $myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
        echo $myJSON;
        exit();
    }


    function insert_sql($str,$conncet)
    {
        try
        {
            $sql = "INSERT INTO `system_setting` (`computer_name`, `c_code`,`add_date`) VALUES ('Web', \"".$str."\",now())";
            $sth = $conncet->exec($sql);
        }
        catch(Exception $e)
        {
            $Data['returnCode'] = "9999";
            $myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
            echo $myJSON;
            exit();
        }
    }

$myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
echo $myJSON;
?>