<?php
require_once Kwf_Config::getValue('externLibraryPath.facebookPhpSdk').'/src/facebook.php';
class Kwf_Util_Facebook_Api extends Facebook
{
    static private $instance = null;

    static public function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct()
    {
    $config = Kwf_Config::getValueArray('kwc.fbAppData');

    parent::__construct($config);

    }
    private function __clone(){}
}
