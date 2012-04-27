<?php
//Service File for Member Controller
//Authored by: Matthew Tieman
class App_Service_MemberService {
	protected $db;
	//Constructor creates a connection to the database
	function __construct(){
		$this->db = Zend_Db_Table::getDefaultAdapter();
	}
        //Returns basic information of all of user's 'Open' cases
	//Called: Automatically called when building the Memeber landing page
        //@Param: $user_id = user_id of the current member
	public function GetUserOpenCases($user_id){
		$select = $this->db->select()
			->from(array('cn' => 'case_need'),
					array('clientID' => 'c.client_id',
					'firstName' => 'c.first_name',
					'lastName' => 'c.last_name',
					'homePhone' => 'c.home_phone',
					'cellPhone' => 'c.cell_phone',
					'workPhone' => 'c.work_phone',
					'caseID' => 'cc.case_id',
					'needs' => new Zend_Db_Expr("GROUP_CONCAT( cn.need SEPARATOR', ')"),
					'totalAmount' => new Zend_Db_Expr('SUM(cn.amount)'), 'openedDate' => 'cc.opened_date',
					'status' => 'cc.status'))
			->joinInner(array('cc' => 'client_case'), 'cn.case_id = cc.case_id')
			->joinLeft(array('h' => 'household'), 'cc.household_id = h.household_id')
			->joinLeft(array('c' => 'client'), 'c.client_id = h.mainclient_id')
			->joinLeft(array('u' => 'user'), 'cc.opened_user_id = u.user_id')
			->group('cc.case_id')
			->where('cc.opened_user_id = ?', $user_id);
		$results = $this->db->fetchAll($select);
		return $this->BuildOpenCases($results);
	}
	//Returns all information of a particular client nessesary to display the client's dossier
	//Called: When an action is generated to display a single client's information
	//@Param: $client_id = client ID# of the client whose information is to be displayed
	public function GetClientById($client_id){
		$select = $this->db->select()
			->from(array('c' => 'client'),
				     array('clientID' => 'c.client_id',
					'createdBy' => 'c.created_user_id',
					'firstName' => 'c.first_name',
					'lastName' => 'c.last_name',
					'otherName' => 'c.other_name',
					'marriage' => 'c.marriage_status',
					'birthday' => 'c.birthdate',
					'ssn4' => 'c.ssn4',
					'cellPhone' => 'c.cell_phone',
					'homePhone' => 'c.home_phone',
					'workPhone' => 'c.work_phone',
					'createdDate' => 'c.created_date',
					'memParish' => 'c.member_parish',
					'vetFlag' => 'c.veteran_flag',
					'street' => 'a.street',
					'aptID' => 'a.apt',
					'city' => 'a.city',
					'state' => 'a.state',
					'zipcode' => 'a.zipcode',
					'doNotHelp' => 'd.client_id'))
			->joinInner(array('h' => 'household'), 'h.mainclient_id = c.client_id AND h.current_flag = 1')
			->joinLeft(array('e' => 'employment'), 'e.client_id = c.client_id')
			->joinLeft(array('u'=> 'user'), 'u.user_id = c.created_user_id')
			->joinLeft(array('a' => 'address'), 'h.address_id = a.address_id')
			->joinLeft(array('d' => 'do_not_help'), 'c.client_id = d.client_id')
			->where('c.client_id = ?', $client_id);
			$results = $this->db->fetchRow($select);
			return $this->BuildClientDossier($results);
	}
	//Returns an array of client case objects, each containing basic information of each case
	//of a particular client
	//Called: At the view client/cases page
	//@Param: $client_id = client ID# of the client whose list of cases is to be displayed
	public function GetClientCases($client_id){
		$select = $this->db->select()
			->from(array('cc' => 'client_case'),
				     array('caseID' => 'cc.case_id',
					   'needs' => new Zend_Db_Expr("GROUP_CONCAT( cn.need SEPARATOR', ')"),
					   'totalAmount' => new Zend_Db_Expr('SUM(cn.amount)'),
					   'dateRequested' => 'cc.opened_date',
					   'status' => 'cc.status',
					   'addByName' => new Zend_Db_Expr("CONCAT(u.first_name,' ', u.last_name)"),
					   'visitDate' => 'visit_date',
					   'hours' => 'hours',
					   'miles' => 'miles'))
			->joinInner(array('h' => 'household'), 'cc.household_id = h.household_id')
			->joinInner(array('c' => 'client'), 'c.client_id = h.mainclient_id')
			->joinInner(array('cn' => 'case_need'), 'cc.case_id = cn.case_id')
			->joinInner(array('u' => 'user'), 'u.user_id = cc.opened_user_id')
			->joinLeft(array('cv' => 'case_visit'), 'cc.case_id = cv.case_id')
			->group('cc.case_id')
			->where('c.client_id = ?', $client_id);
			$results = $this->db->fetchAll($select);
			return $this->BuildClientCases($results);
	}
	//Returns an array of ScheduleWeek objects each containing week number, start date, and
	//the full name of the member on call
	//Called: When generating the sidebar schedule
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
	//Builds an array of Case objects populated with basic information about each case
	//Includes a Client object to hold basic client information with the appropriate  case
	private function BuildOpenCases($results){
		$cases = array();
		foreach($results as $row){
			$case = new Application_Model_Case();
			$client = new Application_Model_Client();

			$client
			->setId($row['clientID'])
			->setFirstName($row['firstName'])
			->setLastName($row['lastName'])
			->setCellPhone($row['cellPhone'])
			->setHomePhone($row['homePhone'])
			->setWorkPhone($row['workPhone']);
			
			$case
			->setId($row['caseID'])
			->setOpenedDate($row['openedDate'])
			->setStatus($row['status'])
			->setNeedList($row['needs'])
			->setTotalAmount($row['totalAmount'])
			->setClient($client);

			$cases[] = $case;
		}
		return $cases;
	}
	//Populates a Client object with all information relevent to the client
	//Client object contains an Addr object to hold detail of current address
	private function BuildClientDossier($results){
		$client = new Application_Model_Client();
		$address = new Application_Model_Addr();
		$address
			->setStreet($results['street'])
			->setApt($results['aptID'])
			->setCity($results['city'])
			->setState($results['state'])
			->setZip($results['zipcode']);
		$client
			->setId($results['clientID'])
			->setUserId($results['createdBy'])
			->setFirstName($results['firstName'])
			->setLastName($results['lastName'])
			->setOtherName($results['otherName'])
			->setMarried($results['marriage'])
			->setBirthDate($results['birthday'])
			->setSsn4($results['ssn4'])
			->setCellPhone($results['cellPhone'])
			->setHomePhone($results['homePhone'])
			->setWorkPhone($results['workPhone'])
			->setCreatedDate($results['createdDate'])
			->setParish($results['memParish'])
			->setVeteran($results['vetFlag'])
			->setDoNotHelp($results['doNotHelp'])
			->setCurrentAddr($address);
		return $client;
	}
	//Builds an array of Case objects each containing details about the appropriot case
	private function BuildClientCases($results){
		$cases = array();
		foreach($results as $row){
			$case = new Application_Model_Case();
			$case
			->setId($results['caseID'])
			->setNeedList($results['needs'])
			->setTotalAmount($results['totalAmount'])
			->setOpenedDate($results['dateRequested'])
			->setStatus($results['status'])
			->setOpenedByName($results['addByName'])
			->setLastVisit($results['visitDate'])
			->setTotalHours($results['hours'])
			->setTotalMils($results['miles']);
			$cases[] = $case;
		}
		return $cases;
	}
	//Builds an array of ScheduleWeek objects each containing the week number
	//start date, and the full name of the member on call
	private function BuildSchedule($results){
		$schedule = array();
		foreach($results as $row){
			$week = new Application_Model_SheduleWeek();
			$week
			->setWeekId($results['week_id'])
			->setStartDate($results['startDate'])
			->setUserName($results['fullUserName']);
			$schedule[] = $week;
		}
		return $schedule;
	}
}