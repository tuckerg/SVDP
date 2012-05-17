<?php

class Application_Model_Member_ViewCaseForm extends Twitter_Bootstrap_Form_Horizontal
{

    private $_readOnly;

    public function __construct(Application_Model_Impl_Case $case, array $comments, array $users)
    {
        parent::__construct(array(
            'action' => self::makeActionUrl($case->getId()),
            'method' => 'post',
            'class' => 'form-horizontal',
            'decorators' => array(
                'PrepareElements',
                array('ViewScript', array(
                    'viewScript' => 'form/view-case-form.phtml',
                    'case' => $case,
                    'readOnly' => &$this->_readOnly,
                )),
                'Form',
            ),
        ));

        $this->_readOnly = ($case->getStatus() === 'Closed');

        if (!$this->_readOnly) {
            $this->addElement('submit', 'closeCase', array(
                'label' => 'Close Case',
                'decorators' => array('ViewHelper'),
                'class' => 'btn btn-danger',
            ));
        }

        $this->addSubForm(
            new Application_Model_Member_CaseVisitRecordListSubForm($users, $this->_readOnly),
            'visitRecordList'
        );

        $this->visitRecordList->setRecords($case->getVisits());

        if (!$this->_readOnly) {
            $this->addElement('textarea', 'commentText', array(
                'label' => 'Text of new comment',
                'dimension' => 8,
                'rows' => 7,
            ));

            $this->addElement('submit', 'addComment', array(
                'label' => 'Add Comment',
                'decorators' => array('ViewHelper'),
                'class' => 'btn btn-success',
            ));
        }
    }

    private static function makeActionUrl($id)
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();
        return $baseUrl->baseUrl(App_Resources::MEMBER . '/viewCase'
            . (($id !== null) ? '/id/' . urlencode($id) : ''));
    }
}
