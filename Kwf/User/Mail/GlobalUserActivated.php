<?php
class Kwf_User_Mail_GlobalUserActivated extends Kwf_User_Mail_Abstract
{
    public function __construct(Kwf_Model_Row_Interface $row)
    {
        $tpl = 'GlobalUserActivation';
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - '.trlKwf('Useraccount activated');
        parent::__construct($tpl, $subject, $row);
    }
}
