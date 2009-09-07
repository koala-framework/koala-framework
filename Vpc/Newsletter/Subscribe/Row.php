<?php
class Vpc_Newsletter_Subscribe_Row extends Vps_Model_Db_Row
    implements Vpc_Mail_Recipient_TitleInterface, Vpc_Mail_Recipient_UnsubscribableInterface
{

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
        return ($this->format == 'text' ?
            Vpc_Mail_Recipient_Interface::MAIL_FORMAT_TEXT :
            Vpc_Mail_Recipient_Interface::MAIL_FORMAT_HTML
        );
    }

    public function getMailGender()
    {
        return ($this->gender == 'male' ?
            Vpc_Mail_Recipient_GenderInterface::MAIL_GENDER_MALE :
            Vpc_Mail_Recipient_GenderInterface::MAIL_GENDER_FEMALE
        );
    }

    public function getMailTitle()
    {
        return ($this->title ? $this->title : '');
    }

    public function mailUnsubscribe()
    {
        $this->unsubscribed = 1;
        $this->save();
    }

    public function getMailUnsubscribe()
    {
        return ($this->unsubscribed ? true : false);
    }
}
