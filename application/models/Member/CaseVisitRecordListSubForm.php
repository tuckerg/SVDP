<?php

class Application_Model_Member_CaseVisitRecordListSubForm
    extends App_Form_RecordListSubFormAbstract {

    private $_users;

    public function __construct(array $users, $readOnly)
    {
        parent::__construct(array(
            'namespace' => 'casevisit',
            'labels' => array(
                'Date',
                'Miles',
                'Hours',
                'Primary Member',
                'Secondary Member',
            ),
            'readOnly' => $readOnly,
            'legend' => 'Case visits:',
            'addRecordMsg' => 'Add Another Visit',
            'noRecordsMsg' => 'No visits listed.',
            'submitMsg' => 'Submit Changes',
        ));

        $this->_users = $users;
    }

    protected function initSubForm($caseVisitSubForm)
    {
        $caseVisitSubForm->addElement('hidden', 'id', array(
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'td', 'openOnly' => true)),
            ),
        ));

        $caseVisitSubForm->addElement('text', 'date', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must choose a start date.'),
                )),
                array('Date', true, array(
                    'format' => 'MM/dd/yyyy',
                    'messages' => array(
                        'dateInvalidDate' => 'Must be properly formatted.',
                        'dateFalseFormat' => 'Must be a valid date.',
                    ),
                )),
            ),
            'decorators' => array(
                'ViewHelper',
                'ElementErrors',
                'Wrapper',
                array('HtmlTag', array('tag' => 'td', 'closeOnly' => true)),
            ),
            'maxlength' => 10,
            'class' => 'span2 date',
        ));

        $caseVisitSubForm->addElement('text', 'miles', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must enter miles.'),
                )),
                array('Digits', true, array(
                    'messages' => array('notDigits' => 'Must be an integer.'),
                )),
                array('LessThan', true, array(
                    'min' => 0,
                    'messages' => array('notGreaterThan' => 'Must not be negative.'),
                )),
            ),
            'decorators' => array(
                'ViewHelper',
                'ElementErrors',
                'Wrapper',
                array('HtmlTag', array('tag' => 'td')),
            ),
            'maxlength' => 11,
            'class' => 'span1',
        ));

        $caseVisitSubForm->addElement('text', 'hours', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must enter miles.'),
                )),
                array('Digits', true, array(
                    'messages' => array('notDigits' => 'Must be an integer.'),
                )),
                array('LessThan', true, array(
                    'min' => 0,
                    'messages' => array('notGreaterThan' => 'Must not be negative.'),
                )),
            ),
            'decorators' => array(
                'ViewHelper',
                'ElementErrors',
                'Wrapper',
                array('HtmlTag', array('tag' => 'td')),
            ),
            'maxlength' => 11,
            'class' => 'span1',
        ));

        $caseVisitSubForm->addElement('select', 'primaryUserId', array(
            'multiOptions' => $this->_users,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must choose a member.'),
                )),
                array('InArray', true, array(
                    'haystack' => array_keys($this->_users),
                    'strict' => true,
                    'messages' => array('notInArray' => 'Must choose a member.'),
                )),
            ),
            'dimension' => 2,
        ));

        $caseVisitSubForm->addElement('select', 'secondaryUserId', array(
            'multiOptions' => $this->_users,
            'validators' => array(
                array('InArray', true, array(
                    'haystack' => array_keys($this->_users),
                    'strict' => true,
                    'messages' => array('notInArray' => 'Must choose a member.'),
                )),
            ),
            'dimension' => 2,
        ));
    }

    protected function getRecord($caseVisitSubForm)
    {
        $caseVisit = new Application_Model_Impl_CaseVisit();
        $caseVisit
            ->setVisitId(App_Formatting::emptyToNull($caseVisitSubForm->id->getValue()))
            ->setVisitDate(App_Formatting::unformatDate($caseVisitSubForm->date->getValue()))
            ->setMiles(App_Formatting::emptyToNull($caseVisitSubForm->miles->getValue()))
            ->setHours(App_Formatting::emptyToNull($caseVisitSubForm->hours->getValue()));
    }

    protected function setRecord($caseVisitSubForm, $caseVisit)
    {
        $caseVisitSubForm->id->setValue($caseVisit->getId());
        $caseVisitSubForm->date->setValue(App_Formatting::formatDate($caseVisit->getDate()));
        $caseVisitSubForm->miles->setValue($caseVisit->getMiles());
        $caseVisitSubForm->hours->setValue($caseVisit->getHours());

        $visitors         = $caseVisit->getVisitors();
        $primaryVisitor   = array_shift($visitors);
        $secondaryVisitor = array_shift($visitors);

        $caseVisitSubForm->primaryUserId->setValue($primaryVisitor->getUserId());
        if ($secondaryVisitor) {
            $caseVisitSubForm->secondaryUserId->setValue($secondaryVisitor->getUserId());
        }
    }
}
