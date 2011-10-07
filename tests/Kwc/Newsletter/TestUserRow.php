<?php
class Kwc_Newsletter_TestUserRow extends Kwf_Model_Row_Abstract
implements Kwc_Mail_Recipient_Interface
{
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
        return 'franz@vivid.kwf';
    }

    public function getMailFormat()
    {
        return Kwc_Mail_Recipient_Interface::MAIL_FORMAT_HTML;
    }

}