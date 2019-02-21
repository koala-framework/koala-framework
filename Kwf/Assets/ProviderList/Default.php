<?php
class Kwf_Assets_ProviderList_Default extends Kwf_Assets_ProviderList_Abstract
{
    private static $_instance;
    protected $_pathTypesCacheId = 'assets-file-paths';

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        $providers = self::getVendorProviders();

        parent::__construct($providers);
    }
}
