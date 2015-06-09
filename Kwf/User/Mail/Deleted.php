<?php
class Kwf_User_Mail_Deleted extends Kwf_User_Mail_Abstract
{
    public function __construct(Kwf_Model_Row_Interface $row)
    {
        $tpl = 'UserDeleted';
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - '.trlKwf('Account deleted');
        parent::__construct($tpl, $subject, $row);
    }
}
