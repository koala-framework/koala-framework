<?php
class Kwf_User_EditRow extends Kwf_Model_Proxy_Row
    implements Kwc_Mail_Recipient_TitleInterface
{
    protected $_changedPasswordData = array();
    protected $_changedOldMail = null;
    protected $_sendDeletedMail = null;
    protected $_saveDeletedLog = false;
    protected $_additionalRolesCache = null;
    protected $_notifyGlobalUserAdded = false;
    protected $_logChangedUser = false;
    protected $_logRoleChanged = false;
    protected $_passwordSet = false;
    private $_sendMails = true; // whether to send mails on saving or not. used for resending emails
    protected $_redirectUrl = false;

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

    public function setRedirectUrl($redirectUrl)
    {
        $this->_redirectUrl = $redirectUrl;
    }

    public function getRedirectUrl()
    {
        return $this->_redirectUrl;
    }

    public function setNotifyGlobalUserAdded($val)
    {
        $this->_notifyGlobalUserAdded = $val;
        return $this;
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!empty($this->_changedPasswordData['password1']) && !empty($this->_changedPasswordData['password2'])) {
            if ($this->_changedPasswordData['password1'] == $this->_changedPasswordData['password2']) {
                $this->setPassword($this->_changedPasswordData['password1']);
            } else {
                throw new Kwf_ClientException(trlKwf('Passwords are different - please try again'));
            }
        }
    }
    public function __set($columnName, $value)
    {
        $noLog = $this->getModel()->getNoLogColumns();
        $noLog = array_merge($noLog, array('created', 'logins', 'last_login', 'last_modified', 'password1', 'password2', 'password', 'password_salt'));
        if (!in_array($columnName, $noLog)) {
            if ($value != $this->__get($columnName)) {
                $this->_logChangedUser = true;
            }
        }
        if ($columnName == 'role' && $value !== $this->role) {
            $this->_logRoleChanged = true;
        }
        if ($columnName == 'email' && strtolower($value) != strtolower($this->email)) {
            $this->_changedOldMail = $this->email;
        }
        if ($columnName == 'deleted' && $value != $this->deleted && $value) {
            $this->_sendDeletedMail = true;
            $this->_saveDeletedLog = true;
        }
        if ($columnName == 'password') {
            if (!$this->password) {
                $this->_passwordSet = 'activate';
            } else {
                $this->_passwordSet = 'password_set';
            }
        }

        if ($columnName == 'password1' || $columnName == 'password2') {
            $this->_changedPasswordData[$columnName] = $value;
        } else {
            parent::__set($columnName, $value);
        }
    }

    public function __get($columnName)
    {
        if ($columnName == 'password1' || $columnName == 'password2') {
            return '';
        } else if ($columnName == 'nickname') {
            if (parent::__isset('nickname') && parent::__get('nickname') != '') {
                return parent::__get('nickname');
            } else {
                return trim($this->firstname . ' ' . substr($this->lastname, 0, 1));
            }
        } else if ($columnName == 'email_gender') {
            return $this->gender == 'male' ?
                Kwc_Mail_Recipient_GenderInterface::MAIL_GENDER_MALE :
                Kwc_Mail_Recipient_GenderInterface::MAIL_GENDER_FEMALE;
        } else if ($columnName == 'email_format') {
            return Kwc_Mail_Recipient_Interface::MAIL_FORMAT_HTML;
        } else {
            return parent::__get($columnName);
        }
    }

    public function __isset($columnName)
    {
        if (
            $columnName == 'password1' ||
            $columnName == 'password2' ||
            $columnName == 'name' ||
            $columnName == 'nickname' ||
            $columnName == 'email_gender' ||
            $columnName == 'email_format'
        ) {
            return true;
        } else {
            return parent::__isset($columnName);
        }
    }

    protected function _beforeInsert()
    {
        parent::_beforeInsert();

        $this->getModel()->lockCreateUser();

        if (Kwf_Registry::get('userModel')->mailExists($this->email)) {
            $this->getModel()->unlockCreateUser();
            throw new Kwf_ClientException(
                trlKwf('An account with this email address already exists')
            );
        }

        $this->created = date('Y-m-d H:i:s');
        $this->deleted = 0;
        if (!$this->password) {
            $this->password = '';
        }

        $authMethods = $this->getModel()->getAuthMethods();
        if (isset($authMethods['password'])) {
            $authMethods['password']->generatePasswordSalt($this);
        }

        if (!$this->gender) $this->gender = '';
    }

    public function save()
    {
        $isInsert = !$this->getCleanValue($this->_getPrimaryKey());
        $ret = parent::save();
        if ($isInsert) {
            $this->sendActivationMail();
        }
        return $ret;
    }

    protected function _afterInsert()
    {
        parent::_afterInsert();
        $this->getModel()->unlockCreateUser();

        $this->writeLog('user_created');
    }

    protected function _afterSave()
    {
        parent::_afterSave();

        if ($this->_notifyGlobalUserAdded) {
            $this->sendGlobalUserActivated();
            $this->_notifyGlobalUserAdded = false;
        }

        if ($this->_passwordSet) {
            $this->writeLog('user_'.$this->_passwordSet);

            $this->_passwordSet = false;
        }
    }

    protected function _beforeUpdate()
    {
        parent::_beforeUpdate();
        if ($this->_changedOldMail) {
            if (Kwf_Registry::get('userModel')->mailExists($this->email)) {
                throw new Kwf_ClientException(
                    trlKwf('An account with this email address already exists')
                );
            }
            $this->sendChangedMailMail($this->_changedOldMail);
        }

        if ($this->_sendDeletedMail) {
            $this->sendDeletedMail();
        }
        if ($this->_saveDeletedLog) {
            $this->writeLog('user_deleted');

            $this->_saveDeletedLog = false;
        }
    }

    protected function _afterUpdate()
    {
        parent::_afterUpdate();
        if ($this->_logChangedUser) {
            $this->writeLog('user_edited');

            $this->_logChangedUser = false;
        }
        if ($this->_logRoleChanged) {
            $this->writeLog('user_role_changed');

            $this->_logRoleChanged = false;
        }
    }

    public function setPassword($password)
    {
        $authMethods = $this->getModel()->getAuthMethods();
        return $authMethods['password']->setPassword($this, $password);
    }

    public function generateActivationToken($type)
    {
        $authMethods = $this->getModel()->getAuthMethods();
        return $authMethods['activation']->generateActivationToken($this, $type);
    }

    public function validateActivationToken($token)
    {
        $authMethods = $this->getModel()->getAuthMethods();
        return $authMethods['activation']->validateActivationToken($this, $token);
    }

    public function getAdditionalRoles()
    {
        if (is_null($this->_additionalRolesCache)) {
            $this->_additionalRolesCache = array();
            $rows = $this->getChildRows('additionalRoles');
            foreach ($rows as $r) {
                $this->_additionalRolesCache[] = $r->additional_role;
            }
        }

        return $this->_additionalRolesCache;
    }

    public function setSendMails($value)
    {
        $this->_sendMails = $value;
    }

    public function getSendMails()
    {
        return $this->_sendMails;
    }

    public function sendLostPasswordMail()
    {
        $kwfRow = $this->getModel()->getKwfUserRowById($this->id);
        foreach (Kwf_Registry::get('userModel')->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Password) {
                if ($auth->sendLostPasswordMail($kwfRow, $kwfRow)) {
                    return true;
                }
            }
        }
        throw new Kwf_Exception("Couldn't send lost password mail");
    }

    public function sendActivationMail()
    {
        if ($this->_sendMails) {
            $row = $this->getModel()->getKwfUserRowById($this->id);
            $mail = new Kwf_User_Mail_Activation($row, $this->getRedirectUrl());
            $mail->send();
            $this->writeLog('user_mail_UserActivation');
        }
    }

    // a global user that exists already in service, but not in web
    public function sendGlobalUserActivated()
    {
        if ($this->_sendMails) {
            $row = $this->getModel()->getKwfUserRowById($this->id);
            $mail = new Kwf_User_Mail_GlobalUserActivated($row);
            $mail->send();
            $this->writeLog('user_mail_GlobalUserActivation');
        }
    }

    public function sendChangedMailMail($oldMail)
    {
        if ($this->_sendMails) {
            $row = $this->getModel()->getKwfUserRowById($this->id);
            $mail = new Kwf_User_Mail_ChangedMail($row);
            $mail->oldMail = $oldMail;
            $mail->send();
            $this->writeLog('user_mail_UserChangedMail');
        }
    }

    public function sendDeletedMail()
    {
        if ($this->_sendMails) {
            $row = $this->getModel()->getKwfUserRowById($this->id);
            $mail = new Kwf_User_Mail_Deleted($row);
            $mail->send();
            $this->writeLog('user_mail_UserDeleted');
        }
    }

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

    public function writeLog($messageType)
    {
        $data = array(
            'user_id' => $this->id,
            'message_type' => $messageType
        );
        $this->getModel()->getDependentModel('Messages')->createRow($data)->save();
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
}
