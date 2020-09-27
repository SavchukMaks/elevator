<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include 'Passangers.php';
class Elevator extends Passangers
{

    protected $isDoorsOpened;
    protected $currentFloor;
    protected $maxFloor;
    public $destinationFloor;
    protected $queue = array('up'=>array(),'down'=>array());
    protected $direction; //up,down,stand


    public function __construct($maxFloor,$destinationFloor)
    {
        $this->maxFloor = (int)$maxFloor;
        $this->destinationFloor = (int)$destinationFloor;
    }

    public function setIsDoorsOpened($isDoorsOpened)
    {
        $this->isDoorsOpened = boolval($isDoorsOpened);
    }

    public function getIsDoorsOpened()
    {
        return $this->isDoorsOpened;
    }

    public function checkDoors()
    {
        if ($this->getIsDoorsOpened()){
            return true;
        }
        return false;
    }

    public function getMaxFloor()
    {
        return $this->maxFloor;
    }

    public function setCurrentFloor($currentFloor)
    {

        $this->currentFloor = $currentFloor;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function setDirection($direction)
    {
        if($this->validateDirection($direction)){
            $this->direction = $direction;
            return true;
        }
        return false;
    }

    public function validateDirection($direction){
        $valid_directions = array('up','down','stand','maintenance');
        if(in_array($direction,$valid_directions)){
            return true;
        }
        return false;
    }

    /**
     * Change elevator direction if going up, changes to down
     */
    public function switchDirection(){
        if($this->direction=='up'){
            return $this->setDirection('down');
        }else{
            return $this->setDirection('up');
        }
    }

    public function getCurrentFloor()
    {
        $currentFloor = $this->currentFloor;
        $maxFloor = $this->maxFloor;
        if ($currentFloor >= $maxFloor) {
            exit( 'Floor can\'t be more than ' . $this->getMaxFloor());
        }
        return $currentFloor = $this->currentFloor;
    }

    public function getDestinationFloor()
    {
        return $this->destinationFloor;
    }

    public function validateDestination()
    {
        $currentFloor = $this->getCurrentFloor();
        $destinationFloor = $this->getDestinationFloor();
        if ($currentFloor === $destinationFloor ){
            return false;
        }
        return true;
    }

    public function validateMaxDestination()
    {
        $destinationFloor = $this->getDestinationFloor();
        $maxFloor = $this->getMaxFloor();

        if ($destinationFloor > $maxFloor){
            return false;
        }
        return true;
    }

    public function moveTo()
    {
        if (!$this->checkDoors()){
            if ($this->validateDestination() && $this->validateMaxDestination()) {
                return 'Move to ' . $this->getDestinationFloor() . ' floor';
            }
            exit( 'Can\'t move, Your current floor is '. $this->currentFloor .
                ' your destinational floor is ' . $this->destinationFloor .
                ' maximum floors are ' . $this->maxFloor);
        }
        exit('Wait till doors will be closed');
    }

}
$lift = new Elevator(10,7);
$passangers = new Passangers();

//set max count of passangers
$passangers->setMaxPassangers(4);

//set current floor
//$lift->setCurrentFloor(5);

//set passangers that will enter
$passangers->setPassangers(3);

//open doors
$lift->setIsDoorsOpened(true);
//loading passengers
echo $passangers->loadPassangers($lift) . "</br>";

//set doors closed
$lift->setIsDoorsOpened(false);
//set destination floor
//$elevator->setDestinationFloor(5);
//move to the destination floor
echo $lift->moveTo() . "</br>";

//set doors opened
$lift->setIsDoorsOpened(true);
//set current floor relative to destination floor
$lift->setCurrentFloor(7);
echo $passangers->unLoadPassangers($lift);