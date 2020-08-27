<?php

class Elevator
{
    protected $maxPassangers;
    protected $isDoorsOpened;
    protected $passangers;
    protected $currentFloor;
    protected $maxFloor;
    protected $destinationFloor;


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

    public function setMaxFloor($maxFloor)
    {
        $this->maxFloor = $maxFloor;
    }

    public function getMaxFloor()
    {
        return $this->maxFloor;
    }

    public function setCurrentFloor($currentFloor)
    {

         $this->currentFloor = $currentFloor;
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

    public function setMaxPassangers($maxPassangers)
    {
       return $this->maxPassangers = $maxPassangers;
    }

    public function getMaxPassangers()
    {
        return $this->maxPassangers;
    }

    public function setPassangers($passangers)
    {
        $this->passangers = $passangers;
    }

    public function getPassangers()
    {
        return $this->passangers;
    }

    public function validatePassanger()
    {
        $passanger = $this->getPassangers();
        $maxPassangers = $this->getMaxPassangers();
        if (intval($passanger) > intval($maxPassangers)){
            return false;
        }
        return true;
    }

    public function loadPassangers()
    {
        if($this->checkDoors()) {
            if ($this->validatePassanger()) {
                return 'Load passangers';
            }
            exit( 'Max passangers is ' . $this->maxPassangers);
        }
        exit('Doors are closed,please wait');
    }

    public function setDestinationFloor($destinationFloor)
    {
        $this->destinationFloor = $destinationFloor;
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

    public function unLoadPassangers()
    {
        if($this->checkDoors()){
            if (!$this->validateDestination()){
                return 'You are on destination floor ' . $this->destinationFloor;
            }
            exit('Elevator is still running');
        }
        exit('Doors are closed,please wait');
    }

}
$lift = new Elevator();
//set max count of floor
$lift->setMaxFloor(10);

//set current floor
$lift->setCurrentFloor(1);
$floor = $lift->getCurrentFloor();

//set max count of passangers
$lift->setMaxPassangers(4);
//set passangers that will enter
$lift->setPassangers(2);

//open doors
$lift->setIsDoorsOpened(true);
//loading passengers
echo $lift->loadPassangers() . "</br>";

//set doors closed
$lift->setIsDoorsOpened(false);
//set destination floor
$lift->setDestinationFloor(5);
//move to the destination floor
echo $lift->moveTo() . "</br>";

//set doors opened
$lift->setIsDoorsOpened(true);
//set current floor relative to destination floor
$lift->setCurrentFloor(5);
echo $lift->unLoadPassangers();