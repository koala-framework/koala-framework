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
            'email', 'password', 'password_salt', 'gender', 'title', 'firstname', 'lastname', 'webcode', 'created', 'logins', 'last_login'
        );
    }

    public static function getWebcode()
    {
        $webCode = Zend_Registry::get('config')->service->users->webcode;
        if (is_null($webCode)) {
            throw new Vps_Exception(trlVps("'service.users.webcode' not defined in config"));
        }
        return $webCode;
    }

    public function __toString()
    {
        $ret = '';
        if ($this->title) $ret .= $this->title.' ';
        if ($this->firstname) $ret .= $this->firstname.' ';
        if ($this->lastname) $ret .= $this->lastname;
        return trim($ret);
    }

    public function toArray()
    {
        $ret = parent::toArray();

        $restClient = new Vps_Rest_Client();
        $restClient->getData($this->id, '');
        $restResult = $restClient->get();

        if ($restResult->status()) {
            foreach ($restResult->data as $key => $value) {
                $ret[(string)$key] = (string) $value;
            }
        }

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
        $this->sendDeletedMail();

        $restClient = new Vps_Rest_Client();
        $restClient->deleteAccount($this->getWebcode(), $this->id);
        $restResult = $restClient->get();

        if (!$restResult->status()) {
            throw new Vps_Exception($restResult->msg());
        }

        parent::_delete();
    }

    public function save()
    {
        // id um update oder insert zu unterscheiden
        $id = 0;
        if ($this->id) $id = $this->id;

        if (!$id) {
            // create: prüfen ob ohne webcode in DIESEM web schon existent
            $restClient = new Vps_Rest_Client();
            $restClient->exists('', $this->_changedServiceData['email']);
            $restResult = $restClient->get();

            if ($restResult->status()) {
                if ($this->getTable()->find((int)$restResult->data->id)->current()) {
                    throw new Vps_ClientException(
                        trlVps('An account with this email address already exists')
                    );
                }
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

        $restClient = new Vps_Rest_Client();
        $restClient->save($this->getWebcode(), $id, $this->_changedServiceData);
        $restResult = $restClient->get();

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

        if (!empty($oldMail)) {
            if ($oldMail != $this->email) {
                $this->sendChangedMailMail($oldMail);
            }
        }

        $allCache = call_user_func(array($this->getTableClass(), 'getAllCache'));
        if (!is_null($allCache)) {
            $this->getTable()->createAllCache();
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
        $subject .= trlVps(' - Useraccount created');
        return $this->_sendMail('UserActivation', $subject);
    }

    public function sendLostPasswordMail()
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= trlVps(' - lost password');
        return $this->_sendMail('UserLostPassword', $subject);
    }

    public function sendChangedMailMail($oldMail)
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= trlVps(' - Email changed');
        return $this->_sendMail(
            'UserChangedMail',
            $subject,
            array('oldMail' => $oldMail)
        );
    }

    public function sendDeletedMail()
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= trlVps(' - Accound deleted');
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

        $activateComponent = null;
        $config = new Zend_Config_Ini('application/config.ini');
        if ($config->pagecollection) {
            $pc = Vps_PageCollection_Abstract::getInstance();
            $userComponent = $pc->getComponentByParentClass('Vpc_User_Component');
            if ($userComponent) {
                $tmpComponents = $pc->getChildPages($userComponent);
                if ($tmpComponents) {
                    foreach ($tmpComponents as $tmpComponent) {
                        if ($tmpComponent instanceof Vpc_User_Activate_Component) {
                            $activateComponent = $tmpComponent;
                            break;
                        }
                    }
                }
            }
        }

        if ($activateComponent) {
            $url = $activateComponent->getUrl();
        } else {
            $url = '/vps/user/login/activate';
        }
        $mail->activationUrl = $mail->webUrl.$url.'?code='.$this->id.'-'.
                        $this->getActivationCode();

        return $mail->send();
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
        static $cache = array();
        if (in_array($columnName, $this->getServiceColumns())) {
            if (isset($this->_changedServiceData[$columnName])) {
                return $this->_changedServiceData[$columnName];
            } else if ($this->id) {
                if (!isset($cache[$this->id])) {
                    $allCache = call_user_func(array($this->getTableClass(), 'getAllCache'));
                    if (!is_null($allCache)) {
                        $cache[$this->id] = $allCache[$this->id];
                    } else {
                        $restClient = new Vps_Rest_Client();
                        $restClient->getData($this->id, '');

                        $restResult = $restClient->get();
                        if (!$restResult->status()) {
                            throw new Vps_Exception($restResult->msg());
                        }
                        $cache[$this->id] = $restResult->data;
                    }
                }
                return (string)$cache[$this->id]->{$columnName};
            }
            return null;
        } else if ($columnName == 'password1' || $columnName == 'password2') {
            return '';
        } else if ($columnName == 'name') {
            return $this->firstname . ' ' . $this->lastname;
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
            $columnName == 'name')
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
