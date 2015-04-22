<?php
class Kwf_User_Mail_LostPassword extends Kwf_User_Mail_Abstract
{
    public function __construct(Kwf_User_Row $row)
    {
        $tpl = 'UserLostPassword';
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - '.trlKwf('lost password');
        parent::__construct($tpl, $subject, $row);

        $this->lostPasswordUrl = $row->getModel()->getUserLostPasswordUrl($row);

    }
}
