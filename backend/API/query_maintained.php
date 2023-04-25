<?PHP
	include_once('../../config/db.php');

    $Data['returnCode'] = "0000";

    try
    {
        $sql = "SELECT (@id := @id+1) as id,(SELECT update_time FROM maintain where room_id=room.id) as update_time,group_concat(center_id) as center_id,concat((SELECT hostname FROM host where dong=room.dong) ,'/',floor,'F') as laction FROM room,(select @id:=0) as tmp where id in (SELECT room_id FROM maintain where is_maintain =1) group by dong,floor,center_id";
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

    $data = array();
    foreach($result as $v)
    {
        $res = array();
        $res['id'] = $v['id'];
        $res['update_time'] = $v['update_time'];
        $stringArr=explode(",",$v['center_id']);
        $res['center_id'] = $stringArr;
        $res['laction'] = $v['laction'];
        array_push($data, $res);
    }
    $Data['data'] = $data;

$myJSON = json_encode($Data,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
echo $myJSON;
?>