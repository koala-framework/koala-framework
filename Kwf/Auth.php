<?php

class Vps_Auth extends Zend_Auth
{

    // Kopiert von Zend_Auth und abgeändert
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();

            // automatisches einloggen
            $autologin = Zend_Registry::get('config')->autologin;
            if ($autologin && Vps_Setup::hasDb()) {
                $storage = self::$_instance->getStorage();
                $loginData = $storage->read();

                if (!$loginData['userId']) {
                    $userModel = Zend_Registry::get('userModel');

                    $r = $userModel->getRow($userModel->select()->whereEquals('email', $autologin));
                    if (!$r) {
                        $msg = "Autologin email '$autologin' does not exists";
                        throw new Vps_Exception("autologin failed: $msg");
                    }

                    $loginData['userId'] = $r->id;
                    $storage->write($loginData);
                }
            }
        }

        return self::$_instance;
    }

    public function clearIdentity()
    {
        $ret = parent::clearIdentity();
        $userModel = Vps_Registry::get('userModel');
        if ($userModel) $userModel->clearAuthedUser();
        return $ret;
    }


}