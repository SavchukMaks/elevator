<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
class Passangers {
    protected $maxPassangers;
    protected $passangers;

    /**
     * @return mixed
     */
    public function getMaxPassangers()
    {
        return $this->maxPassangers;
    }

    /**
     * @return mixed
     */
    public function getPassangers()
    {
        return $this->passangers;
    }

    /**
     * @param mixed $maxPassangers
     */
    public function setMaxPassangers($maxPassangers)
    {
        $this->maxPassangers = $maxPassangers;
    }

    /**
     * @param mixed $passangers
     */
    public function setPassangers($passangers)
    {
        $this->passangers = $passangers;
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

    public function loadPassangers(Elevator $elevator)
    {
        if($elevator->checkDoors()) {
            if ($this->validatePassanger()) {
                return 'Load passangers';
            }
            exit( 'Max passangers is ' . $this->maxPassangers);
        }
        exit('Doors are closed,please wait');
    }

    public function unLoadPassangers(Elevator $elevator)
    {
        if($elevator->checkDoors()){
            if (!$elevator->validateDestination()){
                return 'You are on destination floor ' . $elevator->destinationFloor;
            }
            exit('Elevator is still running');
        }
        exit('Doors are closed,please wait');
    }
}