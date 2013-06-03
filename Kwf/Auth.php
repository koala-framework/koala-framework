<?php
class Kwf_Auth extends Zend_Auth
{
    // Kopiert von Zend_Auth und abgeändert
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();

            // automatisches einloggen
            $autologin = Zend_Registry::get('config')->autologin;
            if ($autologin && Kwf_Setup::hasDb()) {
                $storage = self::$_instance->getStorage();
                $loginData = $storage->read();

                if (!isset($loginData['userId']) || !$loginData['userId']) {
                    $userModel = Zend_Registry::get('userModel');

                    $r = $userModel->getRow($userModel->select()->whereEquals('email', $autologin));
                    if (!$r) {
                        $msg = "Autologin email '$autologin' does not exists";
                        throw new Kwf_Exception("autologin failed: $msg");
                    }

                    $loginData['userId'] = $r->id;
                    $storage->write($loginData);
                }
            }
        }

        return self::$_instance;
    }

    public function getStorage()
    {
        if (null === $this->_storage) {
            $this->setStorage(new Kwf_Auth_Storage_Session());
        }
        return $this->_storage;
    }

    // do not user parent authenticate to prevent writing the identity into the storage
    // this is completely done by user model
    public function authenticate(Zend_Auth_Adapter_Interface $adapter)
    {
        return $adapter->authenticate();
    }

    public function clearIdentity()
    {
        $ret = parent::clearIdentity();
        $userModel = Kwf_Registry::get('userModel');
        if ($userModel) $userModel->clearAuthedUser();
        return $ret;
    }
}