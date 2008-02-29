<?php
class Vps_Model_User_User extends Zend_Db_Table_Row_Abstract
{
    protected $_changedServiceData = array();
    protected $_changedPasswordData = array();

    public function __sleep()
    {
        $ret = parent::__sleep();
        $ret[] = '_changedServiceData';
        $ret[] = '_changedPasswordData';
        return $ret;
    }

    public static function getServiceColumns() {
        return array(
            'email', 'password_salt', 'gender', 'title', 'firstname', 'lastname', 'webcode', 'created', 'logins', 'last_login'
        );
    }

    public static function getWebcode()
    {
        $webCode = Zend_Registry::get('config')->service->users->webcode;
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
                        'An account with this email address already exists'
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

        if (!empty($this->_changedPasswordData['password1']) && !empty($this->_changedPasswordData['password2'])) {
            if ($this->_changedPasswordData['password1'] == $this->_changedPasswordData['password2']) {
                $this->setPassword($this->_changedPasswordData['password1']);
            } else {
                throw new Vps_ClientException('Passwords are different - please try again');
            }
        }

        return parent::save();
    }

    public function sendActivationMail()
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - Account created';
        return $this->_sendMail('mails/UserActivation.txt.tpl', $subject);
    }

    public function sendLostPasswordMail()
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - Lost password';
        return $this->_sendMail('mails/UserLostPassword.txt.tpl', $subject);
    }

    public function sendChangedMailMail($oldMail)
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - Changed email';
        return $this->_sendMail(
            'mails/UserChangedMail.txt.tpl',
            $subject,
            array('oldMail' => $oldMail)
        );
    }

    public function sendDeletedMail()
    {
        $subject = Zend_Registry::get('config')->application->name;
        $subject .= ' - Accound deleted';
        return $this->_sendMail('mails/UserDeleted.txt.tpl', $subject);
    }

    protected function _sendMail($tpl, $subject, $tplParams = null)
    {
        if (!$this->email) {
            return false;
        }

        $webUrl = 'http://'.$_SERVER['HTTP_HOST'];
        $host = parse_url($webUrl, PHP_URL_HOST);
        $hostNonWww = $host;
        if (substr($hostNonWww, 0, 4) == 'www.') {
            $hostNonWww = substr($hostNonWww, 4);
        }
        $activationCode = $this->id.'-'.$this->getActivationCode();

        if (Zend_Registry::get('config')->email) {
            $fromName = Zend_Registry::get('config')->email->from->name;
            $fromAddress = Zend_Registry::get('config')->email->from->address;
        } else {
            $fromName = Zend_Registry::get('config')->application->name;
            $fromAddress = 'noreply@'.$hostNonWww;
        }

        $mailView = new Vps_View_Smarty();
        $mailView->setRenderFile($tpl);

        if (!is_null($tplParams)) {
            foreach ($tplParams as $key => $param) {
                $mailView->{$key} = $param;
            }
        }

        $activateComponent = null;
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

        if ($activateComponent) {
            $activationUrl = $activateComponent->getUrl();
        } else {
            $activationUrl = '/vps/user/login/activate';
        }

        $mailView->webUrl = $webUrl;
        $mailView->host = $host;
        $mailView->activationUrl = $activationUrl;
        $mailView->activationCode = $activationCode;
        $mailView->applicationName = Zend_Registry::get('config')->application->name;
        $mailView->fullname = $this->__toString();
        $mailView->userData = $this->toArray();

        $bodyText = $mailView->render($tpl);

        $mail = new Zend_Mail('utf-8');
        $mail->setBodyText($bodyText);
        $mail->setFrom($fromAddress, $fromName);
        $mail->addTo($this->email, $this->__toString());
        $mail->setSubject($subject);
        $mail->send();

        return true;
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
                    $restClient = new Vps_Rest_Client();
                    $restClient->getData($this->id, '');

                    $restResult = $restClient->get();
                    if (!$restResult->status()) {
                        throw new Vps_Exception($restResult->msg());
                    }
                    $cache[$this->id] = $restResult->data;
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

}
