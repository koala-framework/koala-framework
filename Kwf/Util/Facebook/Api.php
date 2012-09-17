<?php
require_once Kwf_Config::getValue('externLibraryPath.facebookPhpSdk').'/src/FacebookZendSession.php';
class Kwf_Util_Facebook_Api extends FacebookZendSession
{
    static private $instance = null;

    static public function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct($config = null)
    {
        if (!$config) {
            $config = Kwf_Config::getValueArray('kwc.fbAppData');
            if (!isset($config['appId'])) {
                throw new Kwf_Exception('kwc.fbAppData.appId has to be set in config');
            }
            if (!isset($config['secret'])) {
                throw new Kwf_Exception('kwc.fbAppData.secret has to be set in config');
            }
            $fbConfig['appId'] = $config['appId'];
            $fbConfig['secret'] = $config['secret'];
        }
        parent::__construct($fbConfig);
    }
    private function __clone(){}
}
