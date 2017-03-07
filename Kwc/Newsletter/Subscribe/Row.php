<?php
class Kwc_Newsletter_Subscribe_Row extends Kwf_Model_Db_Row
    implements Kwc_Mail_Recipient_TitleInterface, Kwc_Mail_Recipient_UnsubscribableInterface
{
    private $_logSource;
    private $_logIp;

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
        return Kwc_Mail_Recipient_Interface::MAIL_FORMAT_HTML;
    }

    public function getMailGender()
    {
        if ($this->gender == 'male') return Kwc_Mail_Recipient_GenderInterface::MAIL_GENDER_MALE;
        if ($this->gender == 'female') return Kwc_Mail_Recipient_GenderInterface::MAIL_GENDER_FEMALE;
        return null;
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

    public function setLogSource($source)
    {
        $this->_logSource = $source;
    }

    public function getLogSource()
    {
        return $this->_logSource;
    }

    public function setLogIp($ip)
    {
        $this->_logIp = $ip;
    }

    public function getLogIp()
    {
        return ($this->_logIp) ? $this->_logIp : $_SERVER['REMOTE_ADDR'];
    }

    public function writeLog($message, $saveImmediatly = false)
    {
        $childRow = $this->createChildRow('Logs', array(
            'date' => date('Y-m-d H:i:s'),
            'ip' => $this->getLogIp(),
            'message' => $message,
            'source' => $this->getLogSource()
        ));
        if ($saveImmediatly) $childRow->save();
    }
}
