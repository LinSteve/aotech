<?php include_once("config/db.php"); ?>
<?php

class Ezcard
{
    public $id;
    public $username;
    public $Computer_name;
    public $Number;
    public $add_date;
    public $CardID;
    public $member_id;
    public $Sort;
    public $DeviceID;
    public $Run;
    public $BeforeValue;
    public $AutoLoadValue;
    public $PayValue;
    public $SavedValue;
}

class Ezcards
{
    public $ezcards = array();
    
    public function add(Ezcard $ezcard)
    {
        array_push($this->ezcards, $ezcard);
    }

    public function getEzcards():array
    {
        return $this-> ezcards;
    }

    public function getCount():Int
    {
        return count($this-> ezcards);
    }

    public function getMaxId():Int
    {
        $id = 0;
        foreach($this->ezcards as $it)
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
        foreach($this->ezcards as $it)
        {
            if($it->id < $id)
            {
                $id = $it->id ;
            }
        }
        return $id;
    }
   
}


function queryAllOldEzcard():Ezcards
{

    try
    {
        $PDOLink = old_db_conn();
        $sql = "SELECT * FROM ezcard_record";
        $sth = $PDOLink->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll();
    }
    catch(Exception $e)
    {
       echo 'Message: ' .$e->getMessage();
    }
    $its = new Ezcards();
    foreach($result as $v)
    {

        $it = new Ezcard();
        $it -> id = $v['sn'];
        $it -> username = $v['username'];
        $it -> Computer_name = $v['Computer_name'];
        $it -> add_date = $v['Time'];
        $it -> CardID = $v['CardID'];
        $it -> Number = $v['Number'];
        $it -> member_id = $v['member_id'];
        $it -> Sort = $v['Sort'];
        $it -> DeviceID = $v['DeviceID'];
        $it -> Batch_number = $v['Batch_number'];
        $it -> Run = $v['Run'];
        $it -> BeforeValue = $v['BeforeValue'];
        $it -> AutoLoadValue = $v['AutoLoadValue'];
        $it -> PayValue = $v['PayValue'];
        $it -> SavedValue = $v['SavedValue'];
        $its -> add($it);
        $it = null;
    }

    return $its;
}

// $it=queryAllOldEzcard();
// echo $it->getEzcards()[5]->id;
?>


