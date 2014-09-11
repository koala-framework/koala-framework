<?php
class Kwf_User_Row extends Kwf_Model_RowCache_Row
    implements Kwc_Mail_Recipient_TitleInterface
{
    public function __toString()
    {
        $ret = '';
        if ($this->title) $ret .= $this->title.' ';
        if ($this->firstname) $ret .= $this->firstname.' ';
        if ($this->lastname) $ret .= $this->lastname;
        $ret = trim($ret);
        if (!$ret) $ret = $this->email;
        return $ret;
    }

    public function getActivationCode()
    {
        foreach ($this->getModel()->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Password) {
                 $ret = $auth->getActivationCode($this);
                 if ($ret) return $ret;
            }
        }
        throw new Kwf_Exception();
    }

    public function generateAutoLoginToken()
    {
        foreach ($this->getModel()->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_AutoLogin) {
                 $ret = $auth->generateAutoLoginToken($this);
                 if ($ret) return $ret;
            }
        }
        throw new Kwf_Exception();
    }

    public function clearAutoLoginToken()
    {
        foreach ($this->getModel()->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_AutoLogin) {
                 $ret = $auth->clearAutoLoginToken($this);
                 if ($ret) return $ret;
            }
        }
        throw new Kwf_Exception();
    }

    public function setPassword($password)
    {
        foreach ($this->getModel()->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Password) {
                 $ret = $auth->setPassword($this, $password);
                 if ($ret) return $ret;
            }
        }
        throw new Kwf_Exception();
    }

    //moved to model
    protected final function _allowFrontendUrls()
    {}

    // interface Kwc_Mail_Recipient_Interface
    public function getMailGender()
    {
        return $this->email_gender;
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
        return $this->email_format;
    }
}
