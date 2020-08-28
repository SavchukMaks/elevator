# Elevator functional simulation
Create a simple algorithm of elevator functionality. 
```
$elevator = new Elevator();
```
Set max count of floor
```
$elevator->setMaxFloor(10);
```
Set current floor
```
$elevator->setCurrentFloor(1);
```
Set max count of passangers
```
$elevator->setMaxPassangers(4);
```
Set passangers that will enter
```
$elevator->setPassangers(2);
```
Open doors
```
$elevator->setIsDoorsOpened(true);
```
Loading passengers
```
echo $elevator->loadPassangers() . "</br>";
```
Set doors closed
```
$elevator->setIsDoorsOpened(false);
```
Set destination floor
```
$elevator->setDestinationFloor(5);
```
Move to the destination floor
```
echo $elevator->moveTo() . "</br>";
```
Set doors opened
```
$elevator->setIsDoorsOpened(true);
```
Set current floor relative to destination floor
```
$elevator->setCurrentFloor(5);
```
Unloading passagers
```
echo $elevator->unLoadPassangers();
```
