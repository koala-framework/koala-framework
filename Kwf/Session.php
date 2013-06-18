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

            //sessions timeout after 15-20 minutes of inactivity
            //this is in addition to gc_maxlifetime (which isn't reliable enough)
            $sessionTimeout = 20*60;
            if (!isset($_SESSION['kwfTimeout'])) {
                $_SESSION['kwfTimeout'] = time() + $sessionTimeout;
            } else if ($_SESSION['kwfTimeout'] - time() < ($sessionTimeout-5*60)) {
                //extend timeout every 5 minutes (not in every request for better performance)
                $_SESSION['kwfTimeout'] = time() + $sessionTimeout;
            }else if ($_SESSION['kwfTimeout'] - time() < 0) {
                Zend_Session::regenerateId();
            }

        }
    }
}
