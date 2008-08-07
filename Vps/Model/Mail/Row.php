<?php
class Vps_Model_Mail_Row extends Vps_Model_Fnf_Row
{
    protected $_mail;

    public function __construct(array $config)
    {
        parent::__construct($config);

        $tpl = $this->getModel()->getMailTemplate();
        if (!$tpl) {
            throw new Vps_Exception("Mail template not set for model '".get_class($this->getModel())."'");
        }

        $this->_mail = new Vps_Mail($tpl);
    }

    public function save()
    {
        parent::save();
        foreach ($this->_data as $k => $v) {
            $this->_mail->$k = $v;
        }
        $this->_mail->send();
    }

    public function getMail()
    {
        return $this->_mail;
    }

    public function getFrom()
    {
        return $this->_mail->getFrom();
    }

    public function addCc($email, $name = '')
    {
        $this->_mail->addCc($email, $name);
    }

    public function addHeader($name, $value, $append = false)
    {
        $this->_mail->addHeader($name, $value, $append);
    }

    public function addBcc($email)
    {
        $this->_mail->addBcc($email);
    }

    public function setReturnPath($email)
    {
        $this->_mail->setReturnPath($email);
    }

    public function addTo($email, $name = '')
    {
        $this->_mail->addTo($email, $name);
    }

    public function setFrom($email, $name = '')
    {
        $this->_mail->setFrom($email, $name);
    }
}
