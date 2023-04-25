<?php include_once("config/db.php"); ?>
<?php

class Member
{
    public $id;
    public $username;
    public $password;
    public $id_card;
    public $cname;
    public $user_class;
    public $publicCardName;
    public $berth_number;
    public $room_strings;
    public $room_type;
    public $balance;
    public $add_date;
    public $TimeUpdated;
    public $del_mark;
}

class Members
{
    public $members = array();
    
    public function add(Member $member)
    {
        array_push($this->members, $member);
    }

    public function getMembers():array
    {
        return $this-> members;
    }

    public function getCount():Int
    {
        return count($this-> members);
    }

    public function getMaxId():Int
    {
        $id = 0;
        foreach($this->members as $it)
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
        foreach($this->members as $it)
        {
            if($it->id < $id)
            {
                $id = $it->id ;
            }
        }
        return $id;
    }
   
}


function queryAllOldMember():Members
{

    try
    {
        $PDOLink = old_db_conn();
        $sql = "SELECT * FROM member";
        $sth = $PDOLink->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll();
    }
    catch(Exception $e)
    {
       echo 'Message: ' .$e->getMessage();
    }
    $its = new Members();
    foreach($result as $v)
    {

        $it = new Member();
        $it -> id = $v['id'];
        $it -> username = $v['username'];
        $it -> password = $v['password'];
        $it -> id_card = $v['id_card'];
        $it -> cname = $v['cname'];
        $it -> user_class = $v['user_class'];
        $it -> publicCardName = $v['publicCardName'];
        $it -> berth_number = $v['berth_number'];
        $it -> room_strings = $v['room_strings'];
        $it -> room_type = $v['room_type'];
        $it -> balance = $v['balance'];
        $it -> add_date = $v['add_date'];
        $it -> TimeUpdated = $v['TimeUpdated'];
        $it -> del_mark = $v['del_mark'];
        $its -> add($it);
        $it = null;
    }

    try
    {
        $PDOLink = old_db_conn();
        $sql = "SELECT * FROM admin";
        $sth = $PDOLink->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll();
    }
    catch(Exception $e)
    {
       echo 'Message: ' .$e->getMessage();
    }

    foreach($result as $v)
    {


        $it = new Member();
        $it -> id = $its->getMaxId() + 1;
        $it -> username = $v['id'];
        $it -> password = $v['pwd'];
        $it -> id_card ="0000000000";
        $it -> cname = $v['cname'];
        $it -> user_class = "";
        $it -> publicCardName = "管理員";
        $it -> berth_number = "0";
        $it -> room_strings = "";
        $it -> room_type = "";
        $it -> balance = "0";
        $it -> add_date = $v['add_date'];
        $it -> TimeUpdated = $v['add_date'];
        $it -> del_mark = "0";
        $its -> add($it);
        $it = null;
    }

    return $its;
}

// $it=queryAllOldMember();
// echo $it->getMembers()[5]->id;
?>


