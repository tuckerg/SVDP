<?php
//Service File for Admin Controller
//Authored by: Matthew Tieman
class App_Service_AdminService {
    protected $db;
    //Constructor creates a connection to the database
    function __construct(){
	$this->db = Zend_Db_Table::getDefaultAdapter();
    }
    //Returns an array of Member objects each containing basic information of each SVDP member
    public function GetAllMembers(){
        $select = $this->db->select()
            ->from('user');
        $results = $this->db->fetchAll($select);
        return $this->BuildAllMembers($results);
    }
    //Returns an AidLimit object containing current SVDP funds and current limits
    //on aid with respect to amount/case, amount/year, amount in life time, and number of cases
    public function GetFundsAndLimits(){
        $select = $this->db->select()
            ->from('parish_funds');
        $results = $this->db->fetchRow($select);
        return $this->BuildFundsAndLimits($results);
    }
    //Returns an array of ScheduleWeek objects each containing information about which
    //member is on call and when
    public function GetSchedule(){
        $select = $this->db->select()
            ->from(array('s' => 'schedule'),
                   array('week_id',
                   'startDate' => 's.start_date',
                   'fullUserName' => new Zend_Db_Expr("CONCAT(u.first_name,' ', u.last_name)")))
            ->joinLeft(array('u' => 'user'), 's.user_id = u.user_id');
            $results = $this->db->fetchAll($select);
            return $this->BuildSchedule($results);
    }
    //Builds an array of Member objects each containing all information of each SVDP member
    private function BuildAllMembers($results){
        $members = array();
        foreach($results as $row){
            $member = new Application_Model_Impl_Member();
            $member
            ->setUserId($results['user_id'])
            ->setPassword($results['password'])
            ->setFirstName($results['first_name'])
            ->setLastName($results['last_name'])
            ->setEmail($results['email'])
            ->setCellPhone($results['cell_phone'])
            ->setHomePhone($results['home_phone'])
            ->setRole($results['role'])
            ->setNeedChangePassword($results['change_pswd'])
            ->setIsActive($results['active_flag']);
            $members[] = $member;
        }
        return $members;
    }
    //Builds a AidLimit object that contains current SVDP funds and current limits
    //on aid with respect to amount/case, amount/year, amount in life time, and number of cases
    private function BuildFundsAndLimits($results){
        $limits = new Application_Model_Impl_AidLimit();
        $limits
        ->setAvailableFunds($results['available_funds'])
        ->setYearLimit($results['year_limit'])
        ->setLifeLimit($results['lifetime_limit'])
        ->setCaseLimit($results['case_limit'])
        ->setCaseFundLimit($results['casefund_limit']);
        return $limits;
    }
    //Builds an array of ScheduleWeek objects representing the entire schedule
    //each containing information on which SVDP member is on call and when
    private function BuildSchedule($results){
        $schedule = array();
        foreach($results as $row){
            $week = new Application_Model_Impl_SheduleWeek();
            $week
            ->setWeekId($results['week_id'])
            ->setStartDate($results['startDate'])
            ->setUserName($results['fullUserName']);
            $schedule[] = $week;
        }
        return $schedule;
    }
}