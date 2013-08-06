<?php
class Kwf_Session extends Zend_Session
{
    public static function start($options = false)
    {
        //code added here won't be called by Kwf_Session_Namespace -> afterStart
        parent::start($options);
        self::afterStart();
    }

    public static function afterStart()
    {
        static $validatorsRegistered = false;
        if (!$validatorsRegistered) {

            Kwf_Util_Https::ensureHttps();

            if (isset($_SESSION['__KWF']['VALID'])) {
                self::_processValidators();
            }

            //sessions timeout after 15-20 minutes of inactivity
            //this is in addition to gc_maxlifetime (which isn't reliable enough)
            $sessionTimeout = 20*60;
            if (!isset($_SESSION['kwfTimeout'])) {
                $_SESSION['kwfTimeout'] = time() + $sessionTimeout;
            } else if ($_SESSION['kwfTimeout'] - time() < ($sessionTimeout-5*60)) {
                //extend timeout every 5 minutes (not in every request for better performance)
                $_SESSION['kwfTimeout'] = time() + $sessionTimeout;
            } else if ($_SESSION['kwfTimeout'] - time() < 0) {
                $_SESSION = array();
                $_SESSION['kwfTimeout'] = time() + $sessionTimeout;
                Zend_Session::regenerateId();
            }

            if (!isset($_SESSION['__KWF']['VALID'])) {
                Zend_Session::registerValidator(new Kwf_Session_Validator_HttpHost());
                if (Kwf_Setup::getBaseUrl()) {
                    Zend_Session::registerValidator(new Kwf_Session_Validator_BasePath());
                }
                Zend_Session::registerValidator(new Kwf_Session_Validator_RemoteAddr());
            }
            $validatorsRegistered = true;
        }
    }

    //similar to implementation in Zend_Session but don't throw exception (how stupid is that?)
    //instead empty session
    private static function _processValidators()
    {
        foreach ($_SESSION['__KWF']['VALID'] as $validator_name => $valid_data) {
            $validator = new $validator_name;
            if ($validator->validate() === false) {
                $_SESSION = array();
                Zend_Session::regenerateId();
                break;
            }
        }
    }
}
