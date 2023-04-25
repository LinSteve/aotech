<?php include_once("config/db.php"); ?>
<?php include_once("room.php"); ?>
<?php include_once("member.php"); ?>
<?php include_once("ezcard.php"); ?>
<?php include_once("electricrecord.php"); ?>
<?php
$idcard = $_GET['idcard'];

class conversion
{

    private $PDOLink;
    private $Rooms;
    private $Members;
    private $Ezcards;
    private $UseRecords;

    public function __construct($PDOLink,$Rooms,$Members,$Ezcards,$UseRecords)
    {
        $this->PDOLink = $PDOLink;
        $this->Rooms = $Rooms;
        $this->Members = $Members;
        $this->Ezcards = $Ezcards;
        $this->UseRecords = $UseRecords;
    }

    public function conversionAllRoom()
    {
        foreach($this->Rooms->getRooms() as $room)
        {
            $this->conversionRoom($room);
        }
    }

    public function conversionRoom(Room $oldVersionRoom)
    {

        try
        {
            
            $sql = "INSERT INTO `room` (`id`,`name`,`center_id`,`meter_id`,`mode`,`price_degree`,`amount`,`dong`,`floor`,`update_date`,`add_date`,`Title`) 
            VALUES (". $oldVersionRoom->id.",'".$oldVersionRoom->room_number."','".$oldVersionRoom->center_id."','".$oldVersionRoom->meter_id."','".$oldVersionRoom->mode."','".$oldVersionRoom->price_elec_degree."','".$oldVersionRoom->amonut."','".$oldVersionRoom->room_number_type."',
            '".$oldVersionRoom->floor."',now(),now(),'')";
            $sth = $this->PDOLink->exec($sql);
        }
        catch(Exception $e)
        { 
            echo 'Message: ' .$e->getMessage();
        }

    }

    public function conversionRoomForId(Int $room_id)
    {

        foreach($this->Rooms->getRooms() as $room)
        {
            if($room->id == $room_id)
            {
                $this->conversionRoom($room);
                break;
            }
        }
    }

    //conversionMember資料上傳資料庫，其餘function，皆為抓資料處理而已。
    public function conversionMember(Member $it)
    {

        $identity = "0";
        $lockmode = "0";
        $accesslock = "0";
        if($it->room_strings == "C000")
        {
            $it->room_strings  = "";
        }
        if($it->publicCardName=="學生")
        {
            $identity  = 0;
        }else if($it->publicCardName=="公用卡"){
            $identity = 5;
        }
        else if($it->publicCardName=="管理員"){
            $identity = 1;
        }
        try
        {
            
            $sql = "INSERT INTO `member` (`id`,`username`,`cname`,`password`,`access_password`,`id_card`,`room_strings`,`sex`,`balance`,`group_id`,`add_date`,`update_date`,`del_mark`,`identity`,`lockmode`,`accesslock`,`user_class`,`berth_number`) 
            VALUES ('".$it->id."','".$it->username."','".$it->cname."','".$it->password."','88888','".$it->id_card."','".$it->room_strings."','1','".$it->balance."','[]','".$it->add_date."','".$it->TimeUpdated."','".$it->del_mark."',
            '".$identity."','".$lockmode."','".$accesslock."','".$it->user_class."','".$it->berth_number."')";
            $sth = $this->PDOLink->exec($sql);


            $sql = "INSERT INTO `room_electric_situation` (`id`,`member_id`,`room_id`,`powerstaus`,`start_amonut`,`now_amount`,`start_balance`,`now_balance`,`start_date`,`update_date`) 
            VALUES ('".$it->id."','".$it->id."','".$this->Rooms->getRoomId($it->room_strings)."','0','0','0','0','0',now(),now())";
            $sth = $this->PDOLink->exec($sql);
        }
        catch(Exception $e)
        { 
             echo 'Message: ' .$e->getMessage();
        }

    }

    public function conversionMemberForId(Int $memberId)
    {

        foreach($this->Members->getMembers() as $it)
        {
            if($it->id == $memberId)
            {
                $this->conversionMember($it);
                break;
            }
        }
    }

    public function conversionAllMember()
    {
        foreach($this->Members->getMembers() as $it)
        {
            $this->conversionMember($it);
        }
    }


    public function conversionEzcard(Ezcard $it)
    {

        $room_id = "0";
        try
        {
     
            $PDOLink1 = old_db_conn();
            $sql = "SELECT * FROM power_record where member_id=".$it->member_id." ORDER BY start_date limit 1";
            $sth = $PDOLink1->prepare($sql);
            $sth->execute();
            $result = $sth->fetchAll();
        }
        catch(Exception $e)
        {
           echo 'Message: ' .$e->getMessage();
        }

        foreach($result as $v)
        {
    
            $room_id= $v['room_id'];
        }

        try
        {
            
            $sql = "INSERT INTO `ezcard_record` (`username`,`Computer_name`,`Number`,`add_date`,`CardID`,`member_id`,`room_id`,`Sort`,`DeviceID`,`Batch_number`,`Run`,`BeforeValue`,`AutoLoadValue`,`PayValue`,`SavedValue`) 
            VALUES ('".$it->username."','".$it->Computer_name."','".$it->Number."','".$it->add_date."','".$it->CardID."','".$it->member_id."','".$room_id."','".$it->Sort."','".$it->DeviceID."','".$it->Batch_number."','".$it->Run."','".$it->BeforeValue."',
            '".$it->AutoLoadValue."','".$it->PayValue."','".$it->SavedValue."')";
            $sth = $this->PDOLink->exec($sql);

        }
        catch(Exception $e)
        { 
             echo 'Message: ' .$e->getMessage();
        }
    }

    public function conversionEzcardForId(Int $id)
    {

        foreach($this->Ezcards->getEzcards() as $it)
        {
           
            if($it->id == $id)
            {
                $this->conversionEzcard($it);
                break;
            }
        }
    }

    public function conversionAllEzcard()
    {
        foreach($this->Ezcards->getEzcards() as $it)
        {
            $this->conversionEzcard($it);
        }
    }


    
    public function conversionAllUserRecord()
    {
        foreach($this->UseRecords->getUserCords() as $userRecord)
        {
            $this->conversionUserRecord($userRecord);
        }
    }

    public function conversionUserRecord(UseRecord $it)
    {

        try
        {
            
            $sql = "INSERT INTO `room_electric_record` (`room_id`,`member_id`,`price_degree`,`start_amount`,`end_amount`,`start_balance`,`end_balance`,`start_date`,`end_date`) 
            VALUES ('".$it->room_id."','".$it->member_id."','".$it->price_degree."','".$it->start_amount."','".$it->end_amount."','".$it->start_balance."','".$it->end_balance."',
            '".$it->start_date."','".$it->end_date."')";
            $sth = $this->PDOLink->exec($sql);
        }
        catch(Exception $e)
        { 
            echo 'Message: ' .$e->getMessage();
        }

    }

    public function conversionUseRecordForId(Int $userRecord_id)
    {

        foreach($this->UseRecords->getUserCords() as $userRecord)
        {
            if($userRecord->id == $userRecord_id)
            {
                $this->conversionRecord($userRecord);
                break;
            }
        }
    }

    // 建立單筆測試卡號才會用到(使用率較低)
    public function creatTestStudent($id,$idCard,$roomName):Member
    {
   
        $it =new Member();
        $it -> id = $id;
        $it->username = substr($idCard, strlen($roomName)-10).$roomName; //預設卡號+房號
        $it->password = "*7820354FA39E9B967F91EA31D397DC1E788D4D43";
        $it->id_card = $idCard;
        $it->cname = $roomName;
        $it->user_class = "";
        $it->publicCardName = "學生";
        $it->berth_number ="0";
        $it->room_strings = $roomName;
        $it->room_type = "";
        $it->balance =  preg_replace('/[^0-9]/', '', $roomName);
        $it->add_date = "2022-07-11 14:32:16";
        $it->TimeUpdated = "2022-07-11 14:32:16";
        $it->del_mark = "0";
        return $it;
    }

    public function createTestStudentForAllRoom($idcard)
    {
        $this->Rooms = new Rooms();
        $id = $this->getTestStudentMaxId() + 1;
        try
        {
     
            $sql = "SELECT id,name FROM room";
            $sth = $this->PDOLink->prepare($sql);
            $sth->execute();
            $result = $sth->fetchAll();
        }
        catch(Exception $e)
        {
           echo 'Message: ' .$e->getMessage();
        }

        foreach($result as $v)
        {               
            // echo $roomName;
            $room = new Room();
            $room -> id = $v['id'];
            $room -> room_number = $v['name'];
            $this -> Rooms -> addRoom($room);
            $roomName = $v['name'];
            $this->conversionMember($this->creatTestStudent($id,$idcard,$roomName));
            $id++;
        }
        echo $idcard;
    }

    function getTestStudentMaxId():Int
    {
        $id = 0;
        try
        {
     
            $sql = "SELECT IFNULL(Max(id), 0) as id FROM member";
            $sth = $this->PDOLink->prepare($sql);
            $sth->execute();
            $result = $sth->fetchAll();
        }
        catch(Exception $e)
        {
           echo 'Message: ' .$e->getMessage();
        }

        foreach($result as $v)
        {    
           $id = $v['id'];
        }
        return $id;
    }

}

// $Rooms = null;
// $Members = null;
// $Ezcards = null;
// $UseRecord = null;


/* 資料查詢
    $Rooms = queryAllOldRoom(); 房間
    $Members = queryAllOldMember(); 成員資料
    $Ezcards = queryAllOldEzcard(); 儲值紀錄
    $UseRecord = queryAllOldUseRecord(); 使用紀錄

    資料轉換，依據自身需求解除註解即可
    $Conversion->conversionAllRoom(); 房間
    $Conversion->conversionAllMember();成員資料
    $Conversion->conversionAllEzcard(); 儲值紀錄
    $Conversion->conversionAllUserRecord(); 使用紀錄
*/
// $Rooms = queryAllOldRoom();
// $Members = queryAllOldMember();
// $Ezcards = queryAllOldEzcard();
// $UseRecord = queryAllOldUseRecord();
$PDOLink = new_db_conn();
$Conversion = new conversion($PDOLink,$Rooms,$Members,$Ezcards,$UseRecord);
// $Conversion->conversionAllRoom();
// $Conversion->conversionAllMember();
// $Conversion->conversionAllEzcard();
// $Conversion->conversionAllUserRecord();

// 單筆建立測試卡用
// $Conversion->conversionMember($Conversion->creatTestStudent("222222","L101"));

// 測試卡卡號帶入到以下功能內
// $Conversion->createTestStudentForAllRoom("0939564886");
$Conversion->createTestStudentForAllRoom($idcard);
?>
