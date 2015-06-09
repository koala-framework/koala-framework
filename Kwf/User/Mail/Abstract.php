<?php
class Kwf_User_Mail_Abstract
{
    protected $_mail;

    public function __construct($tpl, $subject, Kwf_User_Row $row)
    {
        if (!$row->email) {
            throw new Kwf_Exception("User has no email");
        }
        $this->_mail = new Kwf_Mail_Template($tpl);
        $this->_mail->subject = $subject;
        $this->_mail->addTo($row->email, $row->__toString());

        $this->_mail->fullname = $row->__toString();
        $this->_mail->userData = $row->toArray();
        $this->_mail->loginUrl = $row->getModel()->getUserLoginUrl($row);
    }

    public function __set($key, $val)
    {
        $this->_mail->$key = $val;
    }

    public function __get($key)
    {
        return $this->_mail->$key;
    }

    public function __unset($key)
    {
        unset($this->_mail->$key);
    }

    public function __isset($key)
    {
        return isset($this->_mail->$key);
    }

    public function assign($spec, $value = null)
    {
        return $this->_mail->assign($spec, $value);
    }

    public function send($transport = null)
    {
        return $this->_mail->send($transport);
/*
        $this->getModel()->writeLog(array(
            'user_id' => $row->id,
            'message_type' => 'user_mail_'.$tpl
        ));
*/
    }
}
