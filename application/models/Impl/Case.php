<?php

/**
 * Model class representing a single case, which belongs to some client and which is assigned to
 * some parish member.
 *
 * Note: This class implements the fluent interface pattern, i.e., consecutive set method calls can
 * be chained together: `$case->setId(...)->setOpenedDate(...)` and so on.
 */
class Application_Model_Impl_Case
{

    private $_id = null;

    private $_openedDate = null;

    private $_status = null;

    private $_needList = null;

    private $_totalAmount = null;
    
    private $_clientID = null;

    private $_clientFirstName = null;
    
    private $_clientLastName = null;
    
    private $_openedByName = null;
    
    private $_lastVisit = null;
    
    private $_totalHours = null;
    
    private $_totalMiles = null;
    
    private $_client = null;
    
    private $_clientPhone = null;
    

    /* Generic get/set methods: */

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getOpenedDate()
    {
        return $this->_openedDate;
    }

    public function setOpenedDate($openedDate)
    {
        $this->_openedDate = $openedDate;
        return $this;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function setStatus($status)
    {
        $this->_status = $status;
        return $this;
    }

    public function getNeedList()
    {
        return $this->_needList;
    }

    public function setNeedList($needList)
    {
        $this->_needList = $needList;
        return $this;
    }

    public function getTotalAmount()
    {
        return $this->_totalAmount;
    }

    public function setTotalAmount($totalAmount)
    {
        $this->_totalAmount = $totalAmount;
        return $this;
    }

    public function getClientID()
    {
        return $this->_clientID;
    }

    public function setClientID($clientID)
    {
        $this->_clientID = $clientID;
        return $this;
    }
    
    public function getClientFirstName()
    {
        return $this->_clientFirstName;
    }

    public function setClientFirstName($clientFirstName)
    {
        $this->_clientFirstName = $clientFirstName;
        return $this;
    }
    
    public function getClientLastName()
    {
        return $this->_clientLastName;
    }

    public function setClientLastName($clientLastName)
    {
        $this->_clientLastName = $clientLastName;
        return $this;
    }
    
    public function setOpenedByName($name){
        $this->_openedByName = $name;
        return this;
    }
    
    public function getOpenedByName(){
        return $this->_openedByName;
    }
    
    public function setLastVisit($visit){
        $this->_lastVisit = $visit;
        return $this;
    }
    
    public function getLastVisit(){
        return $this->_lastVisit;
    }
    
    public function setTotalHours($hours){
        $this->_totalHours = $hours;
        return $this;
    }
    
    public function getTotalHours(){
        return $this->_totalHours;
    }
    
    public function setTotalMiles($miles){
        $this->_totalMiles = $miles;
        return $this;
    }
    
    public function getClient(){
        return $this->_client;
    }
    
    public function setClient($client){
        $this->_client = $client;
        return $this;
    }
    
    public function getClientPhone(){
        return $this->_clientPhone;
    }
    
    public function setClientPhone($phone){
        $this->_clientPhone;
        return $this;
    }
}
