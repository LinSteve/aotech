<?PHP
	include_once('../../config/db.php');

	$dong = $_GET['dong'];
    $floor = $_GET['floor'];
    try
    {
        $sql = "
        SELECT r.dong,r.center_id,
                CASE 
                    WHEN COUNT(CASE WHEN is_maintain =1 THEN 1 ELSE NULL END) > 0
                    THEN 'true' 
                    ELSE 'false' 
                END as all_match 
                FROM maintain join room as r on room_id=r.id where r.dong='".$dong."' and r.floor='".$floor."' 
                group by dong,floor,center_id 
                having all_match = 'true'";
        $stmt = $PDOLink -> prepare($sql);
        $stmt -> execute(); 
        $result = $stmt->fetchAll();
    }
    catch(Exception $e)
    {
       
    }

    $Center_ids = array();
    foreach($result as $v)
    {
        array_push($Center_ids, $v['center_id']);
    }
	


$myJSON = json_encode($Center_ids,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
echo $myJSON;
?>