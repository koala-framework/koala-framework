<?php
class Kwf_User_Mail_ChangedMail extends Kwf_User_Mail_Abstract
{
    public function __construct(Kwf_Model_Row_Interface $row)
    {
        $tpl = 'UserChangedMail';
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - '.trlKwf('Email changed');
        parent::__construct($tpl, $subject, $row);
    }
}
