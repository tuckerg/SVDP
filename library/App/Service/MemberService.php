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
	
	public function GetCaseById($case_id){
		$select = $this->db->select()
			->from(array('client_case' => 'cc'),
			       array('clientID' => 'c.client_id',
				     'clientFirstName' => 'c.first_name',
				     'clientLastName' => 'c.last_name',
				     'phone' => new Zend_Db_Expr("COALESCE(c.home_phone, c.cell_phone, c.work_phone)"),
				     'caseID' => $case_id,
				     'needs' => new Zend_Db_Expr("GROUP_CONCAT( cn.need SEPARATOR', ')"),
				     'totalAmount' => new Zend_Db_Expr('SUM(cn.amount)'),
				     'openedDate' => 'cc.opened_date'))
			->joinInner(array('h' => 'household'), 'cc.household_id = h.household_id')
			->joinInner(array('c' => 'client'), 'h.mainclient_id = c.client_id')
			->joinLeft(array('cn' => 'case_need'), 'cn.case_id = cc.case_id')
			->group('cc.case_id')
			->where('cc.case_id = ?', $case_id);
		$results = $this->db->fetchAll($select);
		return $this->BuildCase($results);
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
	//Creates a new client, new address, new household, and if the client is married a
	//client entry for thier spouse as well. The spouse entry will contain only auto generated id,
	//first name, last name, created date, member parish, and the id of the user who created the
	//primary client.
	//Called: When action for client creation is generated
	//@Param: $client = Client object populated with information about client, client object
	//contains an Addr object populated with the address information from the form. 
	public function CreateClient($client){
		$address = $client->getCurrentAddr();
		$date = new Zend_Date();
		$spouseId = null;
		
		$this->InsertClient($client, $date);
		$clientId = $this->db->lastInsertId('client');
		
		//Check if client is married, if so creates an entry for the spouse
		if($client->isMarried()){
			$this->InsertSpouse($client, $date);
			$spouseId = $this->db->lastInsertId('client');
		}
		
		//Create the address entry
		$this->InsertAddress($clientId, $address);
		$addressId = $this->db->lastInsertId('address');
		
		//Create the household entry
		$this->InsertHouseHold($clientId, $spouseId, $addressId);
		
		//Create the employment entry
		$this->InsertEmployment($clientId, $client->getEmployment());
		
		//Update do-not-help
		if($client->isDoNotHelp())
			$this->MarkDoNotHelp($client, $date);
		
	}
	//Builds an array of Case objects populated with basic information about each case
	//Includes a Client object to hold basic client information with the appropriate  case
	private function BuildOpenCases($results){
		$cases = array();
		foreach($results as $row){
			$case = new Application_Model_Impl_Case();
			$client = new Application_Model_Impl_Client();

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
		$client = new Application_Model_Impl_Client();
		$address = new Application_Model_Impl_Addr();
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
			$case = new Application_Model_Impl_Case();
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
			$week = new Application_Model_Impl_SheduleWeek();
			$week
			->setWeekId($results['week_id'])
			->setStartDate($results['startDate'])
			->setUserName($results['fullUserName']);
			$schedule[] = $week;
		}
		return $schedule;
	}
	
	private function BuildCase($results){
		$case = new Application_Model_Impl_Case();
		$case
		->setClientID($results['clientID'])
		->setClientFirstName($results['clientFirstName'])
		->setClientLastName($results['clientLastName'])
		->setClientPhone($results['phone'])
		->setId($results['caseId'])
		->setNeedList($results['needs'])
		->setTotalAmount($results['totalAmount'])
		->setOpenedDate($results['openedDate']);
		return $case;
	}
	
	private function InsertClient($client, $date){
		$clientData = array(
			      'created_user_id' => $client->getUserId(),
			      'first_name' => $client->getFirstName(),
			      'last_name' => $client->getLastName(),
			      'other_name' => $client->getOtherName(),
			      'marriage_status' => $client->isMarried(),
			      'birthdate' => $client->getBirthdate(),
			      'ssn4' => $client->getSsn4(),
			      'cell_phone' => $client->getCellPhone(),
			      'home_phone' => $client->getHomePhone(),
			      'work_phone' => $client->getWorkPhone(),
			      'created_date' => $date->get('YYYY-MM-dd'),
			      'member_parish' => $client->getParish(),
			      'veteran_flag' => $client->isVeteran());
		$this->db->insert('client', $clientData);
	}
	
	private function InsertSpouse($client, $date){
		$spouseData = array(
			'created_user_id' => $client->getUserId(),
		      'first_name' => $client->getSpouseFirst(),
		      'last_name' => $client->getSpouseLast(),
		      'marriage_status' => $client->isMarried(),
		      'created_date' => $date->get('YYYY-MM-dd'),
		      'member_parish' => $client->getParish());
		$this->db->insert('client', $spouseData);
	}
	
	private function InsertAddress($clientId, $address){
		$addressData = array(
			'client_id' => $clientId,
			'street' => $address->getStreet(),
			'apt' => $address->getApt(),
			'city' => $address->getCity(),
			'state' => $address->getState(),
			'zipcode' => $address->getZip(),
			'reside_parish' => $address->getResideParish());
		$this->db->insert('address', $addressData);
	}
	
	private function InsertHouseHold($clientId, $spouseId, $addressId){
		$householdData = array(
			'address_id' => $addressId,
			'mainclient_id' => $clientId,
			'spouse_id' => $spouseId,
			'current_flag' => '1');
		$this->db->insert('household', $householdData);
	}
	
	private function InsertEmployment($client_id, $employment){
		foreach($employment as $job){
			$jobData = array(
				'client_id' => $client_id,
				'company' => $job->getCompany(),
				'position' => $job->getPosition(),
				'start_date' => $job->getStartDate(),
				'end_date' => $job->getEndDate());
			$this->db->insert('employment', $jobData);
		}
	}
	
	private function MarkDoNotHelp($client, $date){
		$noHelpData = array(
				'client_id' => $client->getId(),
				'created_user_id' => $client->setUserId(),
				'added_date' => $date->get('YYYY-MM-dd'));
		$this->db->insert('do_not_help', $noHelpData);
	}
}