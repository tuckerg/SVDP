<?php
//Class represents all limitations on the amount of aid that can be given, number of times aid can be given
//for both cases and client life time, as well as currently available funds
//Implements the fluent interface pattern
class Application_Model_Impl_AidLimit{
    private $availableFunds = null;
    private $yearLimit = null;
    private $lifeLimit = null;
    private $caseLimit = null;
    private $caseFundLimit = null;
    
    public function getAvailableFunds(){
        return $this->availableFunds;
    }
    public function setAvailableFunds($funds){
        $this->availableFunds = $funds;
        return $this;
    }
    public function getYearLimit(){
        return $this->yearLimit;
    }
    public function setYearLimit($limit){
        $this->yearLimit = $limit;
        return $this;
    }
    public function getLifeLimit(){
        return $this->lifeLimit;
    }
    public function setLifeLimit($limit){
        $this->lifeLimit = $limit;
        return $this;
    }
    public function getCaseLimit(){
        return $this->caseLimit;
    }
    public function setCaseLimit($limit){
        $this->caseLimit = $limit;
        return $this;
    }
    public function getCaseFundLimit(){
        return $this->caseFundLimit;
    }
    public function setCaseFundLimit($limit){
        $this->caseFundLimit = $limit;
        return $this;
    }
}