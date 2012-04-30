<?php
//Class represents a single week in the on-call schedule
//Contains week number, start date, and full name of the member on call
class Application_Model_Impl_ScheduleWeek{
    private $weekID = null;
    private $startDate = null;
    private $fullUserName = null;
    
    public function getWeekId(){
        return $this->weekID;
    }
    public function setWeekId($id){
        $this->weekID = $id;
        return $this;
    }
    public function getStartDate(){
        return $this->setStartDate;
    }
    public function setStartDate($date){
        $this->startDate = $date;
        return $this;
    }
    public function getUserName(){
        return $this->fullUserName;
    }
    public function setUserName($name){
        $this->fullUserName = $name;
        return $this;
    }
}