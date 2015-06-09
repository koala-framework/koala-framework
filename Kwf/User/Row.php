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

    public function generateActivationToken($type)
    {
        foreach ($this->getModel()->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Activation) {
                 $ret = $auth->generateActivationToken($this, $type);
                 if ($ret) return $ret;
            }
        }
        throw new Kwf_Exception("No Activation Auth found");
    }

    public function validateActivationToken($token)
    {
        foreach ($this->getModel()->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Activation) {
                 $ret = $auth->validateActivationToken($this, $token);
                 if ($ret) return $ret;
            }
        }
        return false;
    }

    public function isActivated()
    {
        foreach ($this->getModel()->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Activation) {
                 $ret = $auth->isActivated($this);
                 if (!is_null($ret)) return $ret;
            }
        }
        return false;
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

    public function clearActivationToken()
    {
        foreach ($this->getModel()->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Activation) {
                 $ret = $auth->clearActivationToken($this);
                 if ($ret) return $ret;
            }
        }
        throw new Kwf_Exception();
    }

    public function setPassword($password)
    {
        foreach ($this->getModel()->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Redirect) {
                 if (!$auth->allowPasswordForUser($this)) {
                    throw new Kwf_Exception("This user must not have a password for his account");
                 }
            }
        }
        foreach ($this->getModel()->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Password) {
                 $ret = $auth->setPassword($this, $password);
                 if ($ret) return $ret;
            }
        }
        throw new Kwf_Exception();
    }

    public function validatePassword($password)
    {
        foreach ($this->getModel()->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Password) {
                 $ret = $auth->validatePassword($this, $password);
                 if (!is_null($ret)) return $ret;
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
