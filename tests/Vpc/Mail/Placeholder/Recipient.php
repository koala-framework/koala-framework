<?php
class Vpc_Mail_Placeholder_Recipient extends Vps_Model_Row_Data_Abstract implements Vpc_Mail_Recipient_Interface
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