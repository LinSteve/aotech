<?PHP
include_once('../../config/db.php');
$admin = $_SESSION['admin_user']['username'];
// if(!$admin ) alertMsg('請登入', '../session.php', true);

// $id = $_GET['id'];
try
{
	$sql = "
	SELECT * FROM (SELECT * FROM member where room_strings != '' 
	and del_mark = 0 and balance > 10 
	order by ( case when cname like '%公用卡%' then 1 else 10 end) desc) as a 
	group by room_strings;
	";
	$stmt = $PDOLink -> prepare($sql);
	$stmt -> execute(array($id)); 
	$id_data =$stmt -> fetchAll();
	foreach($id_data as $v)
	{
		$hw_cmd = array('op' => 'PowerOn', 'table' => 'member', 'id' => $v['id']);
		insert_system_setting($hw_cmd);
		$content = "控電人員設定; member: {$v['id']}; name: {$v['cname']}; room: {$v['room_strings']}; 管理員: {$admin}";
		func::toLog('後台', $content, $PDOLink); 	
			
	}
	die(header("Location: ../electricmember.php?success=1"));	
}
catch(Exception $e)
{
	die(header("Location: ../electricmember.php?error=1"));			
}
?>