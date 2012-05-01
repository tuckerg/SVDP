<?php
//Class represents all information on a SVDP member
//Implements the fluent interface pattern
class Application_Model_Impl_Member{
    private $userID = null;
    private $password = null;
    private $firstName = null;
    private $lastName = null;
    private $email = null;
    private $cellPhone = null;
    private $homePhone = null;
    private $role = null;
    private $needChangePassword = null;
    private $isActive = null;
    
    public function getUserId(){
        return $this->userID;
    }
    public function setUserId($userID){
        $this->userID;
        return $this;
    }
    public function getPassword(){
        return $this->password;
    }
    public function setPassword($pass){
        $this->password = $pass;
        return $this;
    }
    public function getFirstName(){
        return $this->firstName;
    }
    public function setFirstName($fName){
        $this->firstName = $fName;
        return $this;
    }
    public function getLastName(){
        return $this->lastName;
    }
    public function setLastName($lName){
        $this->lastName = $lName;
        return $this;
    }
    public function getEmail(){
        return $this->email;
    }
    public function setEmail($email){
        $this->email = $email;
        return $this;
    }
    public function getCellPhone(){
        return $this->cellPhone;
    }
    public function setCellPhone($cellPhone){
        $this->cellPhone = $cellPhone;
        return $this;
    }
    public function getHomePhone(){
        return $this->homePhone;
    }
    public function setHomePhone($homePhone){
        $this->homePhone = $homePhone;
        return $this;
    }
    public function getRole(){
        return $this->role;
    }
    public function setRole($role){
        $this->role = $role;
        return $this;
    }
    public function getNeedChangePassword(){
        return $this->needChangePassword;
    }
    public function setNeedChangePassword($needChange){
        $this->needChangePassword = $needChange;
        return $this;
    }
    public function getIsActive(){
        return $this->isActive;
    }
    public function setIsActive($active){
        $this->isActive = $active;
        return $this;
    }
}
