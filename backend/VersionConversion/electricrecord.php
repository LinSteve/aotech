<?php include_once("config/db.php"); ?>
<?php

class UseRecord
{
    public $id;
    public $member_id;
    public $room_id;
    public $price_degree;
    public $start_amount;
    public $end_amount;
    public $start_balance;
    public $end_balance;
    public $start_date;
    public $end_date;
}

class UseRecords
{
    public $useRecords = array();
    
    public function add(UseRecord $useRecord)
    {
        array_push($this->useRecords, $useRecord);
    }

    public function getUserCords():array
    {
        return $this-> useRecords;
    }

    public function getCount():Int
    {
        return count($this-> useRecords);
    }

    public function getMaxId():Int
    {
        $id = 0;
        foreach($this->useRecords as $it)
        {
            if($it->id > $id)
            {
                $id = $it->id ;
            }
        }
        return $id;
    }

    public function getMinId():Int
    {
        $id = $this->getMaxId();
        foreach($this->useRecords as $it)
        {
            if($it->id < $id)
            {
                $id = $it->id ;
            }
        }
        return $id;
    }
   
}


function queryAllOldUseRecord():UseRecords
{

    try
    {
        $PDOLink = old_db_conn();
        $sql = "SELECT * FROM power_record";
        $sth = $PDOLink->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll();
    }
    catch(Exception $e)
    {
       echo 'Message: ' .$e->getMessage();
    }
    $its = new UseRecords();
    foreach($result as $v)
    {

        $it = new UseRecord();
        $it -> id = $v['id'];
        $it -> member_id = $v['member_id'];
        $it -> room_id = $v['room_id'];
        $it -> price_degree = $v['price_degree'];
        $it -> start_amount = $v['Start_power'];
        $it -> end_amount = $v['End_power'];
        $it -> start_balance = $v['Start_balance'];
        $it -> end_balance = $v['End_balance'];
        $it -> start_date = $v['start_date'];
        $it -> end_date = $v['end_date'];
        $its -> add($it);
        $it = null;
    }

    return $its;
}

// $it=queryAllOldUseRecord();
// echo $it->getUserCords()[5]->id;
?>


