<?php
class Kwf_User_Mail_Activation extends Kwf_User_Mail_Abstract
{
    public function __construct(Kwf_Model_Row_Interface $row, $redirectUrl = false)
    {
        $tpl = 'UserActivation';
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - '.trlKwf('Useraccount created');
        parent::__construct($tpl, $subject, $row);

        $this->activationUrl = Kwf_Registry::get('userModel')->getUserActivationUrl($row, $redirectUrl);
    }
}
