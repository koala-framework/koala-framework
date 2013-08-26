<?php
class Kwc_Mail_Redirect_Mail_Recipient extends Kwf_Model_Row_Data_Abstract
    implements Kwc_Mail_Recipient_TitleInterface
{
    public function getMailGender()
    {
        return $this->gender;
    }

    public function getMailTitle()
    {
        return $this->title;
    }

    public function getMailFirstname()
    {
        return $this->firstname;
    }

    public function getMailLastname()
    {
        return $this->lastname;
    }

    public function getMailEmail()
    {
        return $this->email;
    }

    public function getMailFormat()
    {
        return $this->format;
    }

}
