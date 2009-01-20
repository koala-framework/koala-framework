<?php
class Vps_Model_User_User extends Vps_Db_Table_Row_Abstract
{
    protected $_changedServiceData = array();
    protected $_changedPasswordData = array();
    protected $_additionalRolesCache = null;

    public function __sleep()
    {
        $ret = parent::__sleep();
        $ret[] = '_changedServiceData';
        $ret[] = '_changedPasswordData';
        return $ret;
    }

    public static function getServiceColumns() {
        return array(
            'email', 'password', 'password_salt', 'gender', 'title', 'firstname', 'lastname', 'webcode', 'created', 'logins', 'last_login', 'last_modified'
        );
    }

    public static function getCachedColumns() {
        return array(
            'email', 'password', 'gender', 'title', 'firstname', 'lastname', 'webcode', 'created', 'last_modified'
        );
    }

    public static function getWebcode()
    {
        $webCode = Zend_Registry::get('config')->service->users->webcode;
        if (is_null($webCode)) {
            throw new Vps_Exception(("'service.users.webcode' not defined in config"));
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

    public function getActivationCode()
    {
        return substr(md5($this->password_salt), 0, 10);
    }

    public function setPassword($password)
    {
        $restClient = new Vps_Rest_Client();
        $restClient->setPassword($this->id, $password);
        $restResult = $restClient->get();

        if (!$restResult->status()) {
            throw new Vps_Exception($restResult->msg());
        }

        return $restResult->status();
    }

    protected function _delete()
    {
        if (!Vps_Registry::get('config')->allowDeleteUsers) {
            throw new Vps_Exception("Deleting users is not allowed. See config value 'allowDeleteUsers'.");
        }

        $this->sendDeletedMail();

        $restClient = new Vps_Rest_Client();
        $restClient->deleteAccount($this->getWebcode(), $this->id);
        $restResult = $restClient->get();

        if (!$restResult->status()) {
            throw new Vps_Exception($restResult->msg());
        }

        try {
            foreach ($this->findDependentRowset('Vps_Model_User_AdditionalRoles') as $row) {
                $row->delete();
            }
        } catch (Zend_Db_Statement_Exception $e) {
        }

        parent::_delete();
    }

    public function save()
    {
        // id um update oder insert zu unterscheiden
        $id = 0;
        if ($this->id) $id = $this->id;

        if (!$id) {
            if ($this->getTable()->mailExists($this->email)) {
                throw new Vps_ClientException(
                    trlVps('An account with this email address already exists')
                );
            }
        } else {
            // alte email adresse holen um zu prüfen ob sie sich geändert hat
            $restClient = new Vps_Rest_Client();
            $restClient->getData($id, 'email');
            $restResult = $restClient->get();
            if (!$restResult->status()) {
                throw new Vps_ClientException($restResult->msg());
            } else {
                $oldMail = $restResult->email();
            }
        }

        if (count($this->_changedServiceData)) {
            $restClient = new Vps_Rest_Client();
            $restClient->save($this->getWebcode(), $id, $this->_changedServiceData);
            $restResult = $restClient->get();
        }

        if (!$restResult->status() && $restResult->operation() == 'update') {
            // wenn er bereits existiert und inserted wurde, soll er einfach
            // hergenommen werden, darum nur bei update fehlerausgabe
            throw new Vps_ClientException($restResult->msg());
        }

        if ($restResult->id()) {
            $this->id = $restResult->id();
        }

        // rest->status() nur true wenn wirklich angelegt, sonst nur id verwendet, da schon existent
        if (!$id && $restResult->status()) {
            $this->sendActivationMail();
        }

        $this->_changedServiceData = array();

        if (!empty($oldMail) && $oldMail != $this->email) {
            $this->sendChangedMailMail($oldMail);
        }

        if (!empty($this->_changedPasswordData['password1']) && !empty($this->_changedPasswordData['password2'])) {
            if ($this->_changedPasswordData['password1'] == $this->_changedPasswordData['password2']) {
                $this->setPassword($this->_changedPasswordData['password1']);
            } else {
                throw new Vps_ClientException(trlVps('Passwords are different - please try again'));
            }
        }

        return parent::save();
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

    private function _sendMail($tpl, $subject, $tplParams = null)
    {
        if (!$this->email) {
            return false;
        }
        $mail = new Vps_Mail($tpl);
        $mail->subject = $subject;
        $mail->addTo($this->email, $this->__toString());
        if ($tplParams) $mail->assign($tplParams);

        $mail->fullname = $this->__toString();
        $mail->userData = $this->toArray();

        $activateComponent = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_User_Activate_Component');
        if ($activateComponent) {
            $url = $activateComponent->url;
        } else {
            $url = '/vps/user/login/activate';
        }
        $mail->activationUrl = $mail->webUrl.$url.'?code='.$this->id.'-'.
                        $this->getActivationCode();

        return $mail->send();
    }

    public function updateCache(array $columns = array())
    {
        $rowIsDirty = false;
        foreach ($columns as $col => $val) {

            if ($this->$col != $val) {
                parent::__set($col, $val);
                $rowIsDirty = true;
            }
        }
        if ($rowIsDirty) parent::save();
    }

    public function __set($columnName, $value)
    {
        if (in_array($columnName, $this->getServiceColumns())) {
            $this->_changedServiceData[$columnName] = $value;
        } else if ($columnName == 'password1' || $columnName == 'password2') {
            $this->_changedPasswordData[$columnName] = $value;
        } else {
            parent::__set($columnName, $value);
        }
    }

    public function __get($columnName)
    {
        if (in_array($columnName, $this->getServiceColumns())) {
            if (isset($this->_changedServiceData[$columnName])) {
                return $this->_changedServiceData[$columnName];
            } else if ($this->id) {
                if (in_array($columnName, $this->getServiceColumns())) {
                    $this->_table->checkCache();
                }
                // wenn column nicht gecached, service wieder manuell fragen
                if (!in_array($columnName, $this->getCachedColumns())) {
                    $restClient = new Vps_Rest_Client();
                    $restClient->getData($this->id, '');

                    $restResult = $restClient->get();
                    if (!$restResult->status()) {
                        throw new Vps_Exception($restResult->msg());
                    }
                    $res = $restResult->data;
                    return (string)$res->{$columnName};
                } else {

                    return parent::__get($columnName);
                }
            }
            return null;
        } else if ($columnName == 'password1' || $columnName == 'password2') {
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
        if (in_array($columnName, $this->getServiceColumns())) {
            return true;
        } else if (
            $columnName == 'password1' ||
            $columnName == 'password2' ||
            $columnName == 'name' ||
            $columnName == 'nickname')
        {
            return true;
        } else {
            return parent::__isset($columnName);
        }
    }

    public function getAdditionalRoles()
    {
        if (is_null($this->_additionalRolesCache)) {
            $this->_additionalRolesCache = array();
            $rows = $this->findDependentRowset('Vps_Model_User_AdditionalRoles');
            foreach ($rows as $r) {
                $this->_additionalRolesCache[] = $r->additional_role;
            }
        }

        return $this->_additionalRolesCache;
    }

}
