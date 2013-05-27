<?php
class Kwf_Session extends Zend_Session
{
    public static function start($options = false)
    {
        parent::start($options);
        self::afterStart();
    }

    public static function afterStart()
    {
        static $validatorsRegistered = false;
        if (!$validatorsRegistered) {
            if (!isset($_SESSION['__ZF']['VALID'])) {
                Zend_Session::registerValidator(new Kwf_Session_Validator_HttpHost());
                Zend_Session::registerValidator(new Kwf_Session_Validator_RemoteAddr());
            }
            $validatorsRegistered = true;
        }
    }
}
