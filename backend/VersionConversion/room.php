<?php include_once("config/db.php"); ?>
<?php

class Room
{
    public $id;
    public $room_number_type;
    public $floor;
    public $room;
    public $room_number;
    public $price_elec_degree;
    public $center_id;
    public $meter_id;
    public $amonut;
    public $mode;
    public $data_type;
}

class Rooms
{
    public $rooms = array();
    
    public function addRoom(Room $room)
    {
        array_push($this->rooms, $room);
    }

    public function getRooms():array
    {
        return $this-> rooms;
    }

    public function getCount():Int
    {
        return count($this-> rooms);
    }

    public function getMaxId():Int
    {
        $id = 0;
        foreach($this->rooms as $room)
        {
            if($room->id > $id)
            {
                $id = $room->id ;
            }
        }
        return $id;
    }

    public function getMinId():Int
    {
        $id = $this->getMaxId();
        foreach($this->rooms as $room)
        {
            if($room->id < $id)
            {
                $id = $room->id ;
            }
        }
        return $id;
    }

    public function getRoomId($roomName):Int
    {
        foreach($this->rooms as $it)
        {
            if($it->room_number == $roomName)
            {
                return $it->id;
            }
        }
        return 0;
    }
   
}


function queryAllOldRoom():Rooms
{

    try
    {
        $PDOLink = old_db_conn();
        $sql = "SELECT * FROM room";
        $sth = $PDOLink->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll();
    }
    catch(Exception $e)
    {
       echo 'Message: ' .$e->getMessage();
    }
    $rooms = new Rooms();
    foreach($result as $v)
    {

        $room = new Room();
        $room -> id = $v['id'];
        $room -> room_number_type = $v['room_number_type'];
        $room -> floor = $v['floor'];
        $room -> room = $v['room'];
        $room -> room_number = $v['room_number'];
        $room -> price_elec_degree = $v['price_elec_degree'];
        $room -> center_id = $v['center_id'];
        $room -> meter_id = $v['meter_id'];
        $room -> amonut = $v['amonut'];
        $room -> mode = $v['mode'];
        $room -> data_type = $v['data_type'];
        $rooms -> addRoom($room);
        $room = null;
    }
    return $rooms;
}


// $Rooms = queryAllOldRoom();
// echo $Rooms->getRoomId("L1212");
?>