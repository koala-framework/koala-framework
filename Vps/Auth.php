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
            if ($autologin) {
                $storage = self::$_instance->getStorage();
                $loginData = $storage->read();

                if (!$loginData['userId']) {
                    $userModel = Zend_Registry::get('userModel');

                    $restClient = new Vps_Rest_Client();
                    $restClient->exists($userModel->getRowWebcode(), $autologin);
                    $restResult = $restClient->get();

                    if (!$restResult->status()) {
                        $msg = $restResult->msg();
                        throw new Vps_Exception("autologin failed: $msg");
                    }

                    $loginData['userId'] = $restResult->id();
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
        if ($userModel) { $userModel->clearAuthedUser(); }
        return $ret;
    }


}