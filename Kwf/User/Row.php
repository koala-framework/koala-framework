<?php
class Kwf_User_Row extends Kwf_Model_RowCache_Row
    implements Kwf_User_RowInterface, Kwc_Mail_Recipient_TitleInterface
{
    protected $_changedPasswordData = array();
    protected $_changedOldMail = null;
    protected $_sendDeletedMail = null;
    protected $_saveDeletedLog = false;
    protected $_additionalRolesCache = null;
    protected $_notifyGlobalUserAdded = false;
    protected $_logChangedUser = false;
    protected $_passwordSet = false;
    private $_sendMails = true; // whether to send mails on saving or not. used for resending emails

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

    public function setNotifyGlobalUserAdded($val)
    {
        $this->_notifyGlobalUserAdded = $val;
        return $this;
    }

    public function getActivationCode()
    {
        return substr(md5($this->password_salt), 0, 10);
    }

    public function getActivationUrl()
    {
        $root = Kwf_Component_Data_Root::getInstance();
        $activateComponent = null;
        if ($root) {
            // todo: ganz korrekt müsste der Benutzer der anlegt eine Sprache
            // für den Benutzer auswählen
            // oder man leitet auf eine redirect seite um und schaut auf die
            // browser accept language
            $activateComponent = $root
                ->getComponentByClass('Kwc_User_Activate_Component', array('limit' => 1));
        }
        $activateUrl = '/kwf/user/login/activate';
        if ($activateComponent) $activateUrl = $activateComponent->url;
        $activationUrl = $activateUrl.'?code='.$this->id.'-'.
                        $this->getActivationCode();
        return $activationUrl;
    }

    public function encodePassword($password)
    {
        return md5($password.$this->password_salt);
    }

    public function validatePassword($password)
    {
        $passCol = $this->getModel()->getPasswordColumn();
        if ($password === md5($this->$passCol) // für cookie login
            || $this->encodePassword($password) === $this->$passCol
        ) {
            return true;
        }
        return false;
    }

    public function generatePasswordSalt()
    {
        mt_srand((double)microtime()*1000000);
        $this->password_salt = substr(md5(uniqid(mt_rand(), true)), 0, 10);
    }

    public function setPassword($password)
    {
        if (!$this->password) {
            $this->_passwordSet = 'activate';
        } else {
            $this->_passwordSet = 'password_set';
        }

        $this->generatePasswordSalt();
        $this->password = $this->encodePassword($password);
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

    protected function _beforeInsert()
    {
        parent::_beforeInsert();

        $this->getModel()->lockCreateUser();

        if ($this->getModel()->mailExists($this->email)) {
            $this->getModel()->unlockCreateUser();
            throw new Kwf_ClientException(
                trlKwf('An account with this email address already exists')
            );
        }

        $this->created = date('Y-m-d H:i:s');
        $this->deleted = 0;
        $this->locked = 0;
        $this->password = '';
        $this->generatePasswordSalt();
        if (!$this->gender) $this->gender = '';
    }

    protected function _beforeUpdate()
    {
        parent::_beforeUpdate();
        if ($this->_changedOldMail) {
            if ($this->getModel()->mailExists($this->email)) {
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
            $this->getModel()->writeLog(array(
                'user_id' => $this->id,
                'message_type' => 'user_deleted'
            ));

            $this->_saveDeletedLog = false;
        }
    }


    protected function _afterUpdate()
    {
        parent::_afterUpdate();
        if ($this->_logChangedUser) {
            $this->getModel()->writeLog(array(
                'user_id' => $this->id,
                'message_type' => 'user_edited'
            ));

            $this->_logChangedUser = false;
        }
    }

    protected function _afterInsert()
    {
        parent::_afterInsert();
        $this->getModel()->unlockCreateUser();

        if (!$this->password) {
            $this->sendActivationMail();
        }

        $this->getModel()->writeLog(array(
            'user_id' => $this->id,
            'message_type' => 'user_created'
        ));
    }

    protected function _afterSave()
    {
        parent::_afterSave();

        if ($this->_notifyGlobalUserAdded) {
            $this->sendGlobalUserActivated();
            $this->_notifyGlobalUserAdded = false;
        }

        if ($this->_passwordSet) {
            $this->getModel()->writeLog(array(
                'user_id' => $this->id,
                'message_type' => 'user_'.$this->_passwordSet
            ));

            $this->_passwordSet = false;
        }
    }

    public function setSendMails($value)
    {
        $this->_sendMails = $value;
    }

    // a global user that exists already in service, but not in web
    public function sendGlobalUserActivated()
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - '.trlKwf('Useraccount activated');
        return $this->_sendMail('GlobalUserActivation', $subject);
    }

    public function sendActivationMail()
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - '.trlKwf('Useraccount created');
        return $this->_sendMail('UserActivation', $subject);
    }

    public function sendLostPasswordMail()
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - '.trlKwf('lost password');
        return $this->_sendMail('UserLostPassword', $subject);
    }

    public function sendChangedMailMail($oldMail)
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - '.trlKwf('Email changed');
        return $this->_sendMail(
            'UserChangedMail',
            $subject,
            array('oldMail' => $oldMail)
        );
    }

    public function sendDeletedMail()
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - '.trlKwf('Account deleted');
        return $this->_sendMail('UserDeleted', $subject);
    }

    protected function _createMail($tpl, $subject, $tplParams = null)
    {
        $mailClass = $this->getModel()->getMailClass();
        $mail = new $mailClass($tpl);
        $mail->subject = $subject;
        $mail->addTo($this->email, $this->__toString());
        if ($tplParams) $mail->assign($tplParams);

        $mail->fullname = $this->__toString();
        $mail->userData = $this->toArray();

        $root = Kwf_Component_Data_Root::getInstance();
        $lostPasswortComponent = null;
        if ($root) {
            // todo: ganz korrekt müsste der Benutzer der anlegt eine Sprache
            // für den Benutzer auswählen
            // oder man leitet auf eine redirect seite um und schaut auf die
            // browser accept language
            $lostPasswortComponent = $root
                ->getComponentByClass('Kwc_User_LostPassword_SetPassword_Component', array('limit' => 1));
        }
        $mail->activationUrl = $mail->webUrl . $this->getActivationUrl();
        $lostPassUrl = '/kwf/user/login/activate';
        if ($lostPasswortComponent) $lostPassUrl = $lostPasswortComponent->url;
        $mail->lostPasswordUrl = $mail->webUrl.$lostPassUrl.'?code='.$this->id.'-'.
                        $this->getActivationCode();
        return $mail;
    }


    protected function _sendMail($tpl, $subject, $tplParams = null)
    {
        if (!$this->email || !$this->_sendMails) {
            return false;
        }
        $mail = $this->_createMail($tpl, $subject, $tplParams);
        $ret = $mail->send();

        $this->getModel()->writeLog(array(
            'user_id' => $this->id,
            'message_type' => 'user_mail_'.$tpl
        ));

        return $ret;
    }

    public function __set($columnName, $value)
    {
        $noLog = $this->getModel()->getNoLogColumns();
        $noLog = array_merge($noLog, array('webcode', 'created', 'logins', 'last_login', 'last_modified', 'locked'));
        if (!in_array($columnName, $noLog)) {
            $this->_logChangedUser = true;
        }
        if ($columnName == 'email' && $value != $this->email) {
            $this->_changedOldMail = $this->email;
        }
        if ($columnName == 'deleted' && $value != $this->deleted && $value) {
            $this->_sendDeletedMail = true;
            $this->_saveDeletedLog = true;
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
        } else if ($columnName == 'name') {
            return $this->firstname . ' ' . $this->lastname;
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
