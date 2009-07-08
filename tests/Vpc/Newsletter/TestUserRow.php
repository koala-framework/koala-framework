<?php
class Vpc_Newsletter_TestUserRow extends Vps_Model_Row_Abstract
implements Vpc_Mail_Recipient_Interface
{
    public function getMailGender()
    {
        return Vpc_Mail_Recipient_Interface::MAIL_GENDER_MALE;
    }

    public function getMailTitle()
    {
        return 'Mag.';
    }

    public function getMailFirstname()
    {
        return 'Franz';
    }

    public function getMailLastname()
    {
        return 'Unger';
    }

    public function getMailEmail()
    {
        return 'franz@vivid.vps';
    }

    public function getMailFormat()
    {
        return Vpc_Mail_Recipient_Interface::MAIL_FORMAT_HTML;
    }

}