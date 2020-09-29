<?php

class Elevator2{

    protected $direction = 'up';//up,down,stand,maintenance
    protected $current_floor = 1;
    protected $queue = array('up'=>array(),'down'=>array());
    protected $signal=  'door_close';//door_open,door_close,alarm
    protected $max_floor = 1;
    protected $maintenance_floors = array();
    protected $state = 'stand';//up,down,stand,maintenance

    public function __construct($total_floors=3){
        $this->setTotalFloors($total_floors);
    }

    public function getTotalFloors(){
        return $this->max_floor;
    }

    public function setCurrentFloor($floor){
        if(intval($floor)==0){
            $floor = 1;
        }
        $this->current_floor = intval($floor);
        return true;
    }

    public function getCurrentFloor(){
        return $this->current_floor;
    }

    public function getDirection(){
        return $this->direction;
    }


    public function setDirection($direction){
        if($this->validateDirection($direction)){
            $this->direction = $direction;
            return true;
        }
        return false;
    }

    public function switchDirection(){
        if($this->direction=='up'){
            return $this->setDirection('down');
        }else{
            return $this->setDirection('up');
        }
    }


    public function setSignal($signal){
        $valid_signals = array('alarm','door_open','door_close');
        if(in_array($signal,$valid_signals)){
            $this->signal = $signal;
            return true;
        }
        exit('Cant set Signal');
        return false;
    }

    public function getSignal(){
        return $this->signal;
    }

    public function setFloorInMaintenance($floor){
        if(!$this->validateFloor($floor)){
            return false;
        }
        $this->maintenance_floors[] = $floor;
        $this->maintenance_floors = array_unique($this->maintenance_floors);
        return true;
    }

    public function setMaintenance($floors){
        $this->maintenance_floors = array();
        if(!is_array($floors)){
            return true;
        }
        if(sizeof($floors)==0){
            return true;
        }
        $b = true;
        foreach($floors as $k=>$floor){
            $b &= $this->setFloorInMaintenance($floor);
        }
        $this->maintenance_floors = array_unique($this->maintenance_floors);
        return $b;
    }

    public function getMaintenanceFloors(){
        return array_unique($this->maintenance_floors);
    }


    public function isFloorInMaintenance($floor){
        if(!$this->validateFloor($floor)){
            return false;
        }
        return in_array($floor,$this->maintenance_floors);
    }

    public function getNearestFloor(){
        $nRequest = $this->getTotalPendingRequest('both');
        if($nRequest==0){
            return $this->getCurrentFloor();
        }
        $this->getQueue('both');
        $nRequestDown = $this->getQueue('down');//number of request to down
        $nRequestUp = $this->getQueue('up');//number of request to up
        $nDiffToRoof = $this->max_floor - $this->current_floor;//floors to Roof
        $nDiffToGround = $this->current_floor-1;//floors to Ground
        if($nDiffToGround==$nDiffToRoof){
            if($nRequestUp>$nRequestDown) {
                $this->setDirection('up');
                return current($this->queue['up']);
            }
        }else{
            $this->setDirection('down');
            return current($this->queue['down']);
        }
    }

    // Calculate the next floor according to requests
    public function nextFloor(){
        if($this->getTotalPendingRequest('both')==0){
            $this->switchDirectionIfisNecesary();
            return $this->getCurrentFloor();
        }

        echo('queue[up]  :'.implode(',',$this->queue['up'])) . PHP_EOL;
        echo('queue[down]:'.implode(',',$this->queue['down'])) . PHP_EOL;
        $startFloor = $this->getCurrentFloor();//in the same direction
        $nRequest = $this->getTotalPendingRequest($this->direction);
        echo('nRequest:'.$nRequest);
        if($nRequest==0){
            echo('switchDirection:'.$this->direction);
            if(!$this->switchDirection()){
                echo('Cant switch direction.');
            }
            echo('new Direction is:'.$this->direction);
            echo('nRequest['.$this->direction.']:'.$this->getTotalPendingRequest($this->direction));
            if($this->getTotalPendingRequest($this->direction)==0){
                $this->switchDirectionIfisNecesary();
                return 	$this->getCurrentFloor();
            }
        }
        //set currentFloor is the first in the queue[this->direction]
        $this->setCurrentFloor(current($this->queue[$this->direction]));
        echo('current_floor:'.$this->getCurrentFloor() );
        //remove the first in the queue[this->direction]
        echo('remove from queue['.$this->direction .'] floor '.$this->getCurrentFloor());
        if(!$this->removeFloorFromQueue($this->getCurrentFloor(), $this->direction) ){
            echo('Cant remove '.$this->getCurrentFloor().' from queue '.$this->direction.' switch direction');
        }else{
            echo('F'.$this->getCurrentFloor().' removed from queue '.$this->direction);
        }
        echo($startFloor.' - ' .$this->getCurrentFloor());
        return $this->getCurrentFloor();
    }

    //if last change direction to down
    public function switchDirectionIfisNecesary(){
        $floor = $this->getCurrentFloor();
        if(intval($floor) >= $this->max_floor){
            echo('floor '.$this->max_floor.' change direction DOWN');
            return $this->setDirection('down');
        }elseif(intval($floor)<=1){
            echo ('floor 1 change direction UP');
            return $this->setDirection('up');
        }
        echo('No direction changes required!');
        return false;
    }

    //add request to the queue of elevator
    public function pressButton($fromFloor,$direction){
        if(!$this->validateFloor($fromFloor)){
            return false;
        }
        $this->queue[$direction][] = $fromFloor;
        $this->sortQueue($direction);
        return true;
    }

    //add request in a queue
    public function addQueue($fromFloor,$toFloor){
        if(!$this->validateFloor($fromFloor) ){
            return false;
        }
        if(!$this->validateFloor($toFloor) ){
            return false;
        }
        //Cant go to the same floor
        if($fromFloor==$toFloor){
            $this->setSignal('door_open');//Open the door
            return false;
        }
        //if origin floor its highest than destiny means goin down
        if($fromFloor>$toFloor){
            //Going Down
            if($this->pressButton($fromFloor, 'down')){
                return $this->pressButton($toFloor, 'down');
            }
        }else{
            //Going UP
            if($this->pressButton($fromFloor, 'up')){
                return $this->pressButton($toFloor, 'up');
            }
        }
        return false;

    }

    public function getQueue($direction='both'){
        switch($direction){
            case 'up':
                return $this->queue['up'];
                break;
            case 'down':
                return $this->queue['down'];
                break;
            default:
                return $this->queue;
        }

    }

    public function setQueue($direction,$queue = array() ){
        if($this->validateDirection($direction)){
            if((is_array($queue))  && sizeof($queue)>0 ){
            }elseif(gettype($queue)=='string'){
                $queue = explode(',',$queue);
            }
            $tmp = array();
            foreach($queue as $floor){
                if($this->validateFloor($floor)){
                    $tmp[] =$floor;
                }
            }
            $this->queue[$direction] = array_unique($tmp);
            $this->sortQueue($direction);
            return true;
        }
        return false;
    }

    //Calculate the numbert of pending requests
    public function getTotalPendingRequest($direction){
        $nPendingRequestUP = sizeof($this->queue['up']);
        $nPendingRequestDown = sizeof($this->queue['down']);
        switch($direction){
            case 'up':
                return $nPendingRequestUP;
                break;
            case 'down':
                return $nPendingRequestDown;
                break;
            default:
                return $nPendingRequestUP+$nPendingRequestDown;
        }
    }

    protected function validateDirection($direction){
        $valid_directions = array('up','down','stand','maintenance');
        if(in_array($direction,$valid_directions)){
            return true;
        }
        return false;
    }

    // Sort queue by direction
    protected function sortQueue($direction){
        if($direction=='up'){
            $this->queue['up']=array_unique($this->queue['up'],SORT_NUMERIC);
            sort($this->queue['up']);
        }else{
            $this->queue['down']=array_unique($this->queue['down'],SORT_NUMERIC);
            rsort($this->queue['down']);
        }
        return true;
    }

    protected function removeFloorFromQueue($floor,$direction){
        if(!$this->validateDirection($direction)){
            return false;
        }
        if(!$this->validateFloor($floor)){
            return false;
        }
        $position = array_search($floor, $this->queue[$direction]);
        if(($position!==false) && isset($this->queue[$direction][$position])){
            array_shift($this->queue[$direction]);
            reset($this->queue[$direction]);
            $this->sortQueue($direction);
            return true;
        }
        return false;
    }

    protected function getNextStop(){
        if(sizeof($this->queue[$this->direction])==0){
            echo('No pending request return current floor '.$this->getCurrentFloor()  );
            return $this->current_floor;
        }
        echo'queue up:  '.implode(',',$this->queue['up']);
        echo'queue down:  '.implode(',',$this->queue['down']);
        return current($this->queue[$this->direction]);
    }

    public function getNextFloor(){
        $floor_before = $this->getCurrentFloor();
        $next_floor = $this->nextFloor();//return $this->elevator->current_floor
        $data = array(
            'floor_before'=>$floor_before,
            'current_floor'=>$next_floor
        );
        return  'nextFloor '.$this->getDirection().' is '.$next_floor;
    }

    protected function validateFloor($floor){
        if(intval($floor) >= $this->max_floor){
            $floor = $this->max_floor;
        }elseif($floor<=1){
            $floor = 1;
        }
        return true;
    }

    public function setTotalFloors($total_floors){
        $this->max_floor = ( intval($total_floors)==0?1:intval($total_floors));
    }

}
$elevator = new Elevator2(7);
$elevator->setCurrentFloor(1);//current_floor
echo 'Current_floor:'. $elevator->getCurrentFloor() . "<br>";
echo  'Total_floors: '. $elevator->getTotalFloors(). "<br>";
$elevator->setDirection('up');
echo 'Direction: '. $elevator->getDirection() . "<br>";

$maintenance_floors = array(2,4);
foreach($maintenance_floors as $floor){
    $elevator->setFloorInMaintenance($floor);
}
$requests = array(
    array('from'=>6,'to'=>1),
    array('from'=>5,'to'=>7),
    array('from'=>3,'to'=>1),
    array('from'=>1,'to'=>7)
);

foreach($requests as $floor){
    echo'addRequest From F'.$floor['from'].' to F'.$floor['to'] . "<br>";
    $elevator->addQueue($floor['from'],$floor['to']);
}

$nRequest = $elevator->getTotalPendingRequest('both');
if($nRequest>0){
    $queue = $elevator->getQueue();
    echo 'Queue[up]:   '.implode(',',$queue['up']) . "<br>";
    echo 'Queue[down]: '.implode(',',$queue['down']) . "<br>";
    for($i=1;$i<=$nRequest;$i++){
        $before = $elevator->getCurrentFloor() ;
        $elevator->nextFloor();
        echo $before.' to '.$elevator->getCurrentFloor(). ' '.$elevator->getDirection() . "<br>";
    }
}else{
    exit('No request pending');
}
