<?php

class App_Service_TreasurerService{
    protected $db;
	//Constructor creates a connection to the database
	function __construct(){
		$this->db = Zend_Db_Table::getDefaultAdapter();
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
}