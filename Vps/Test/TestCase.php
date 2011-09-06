<?php
class Vps_Test_TestCase extends PHPUnit_Framework_TestCase
{
    protected $backupStaticAttributes = false;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass(false);
        Vps_Component_Cache::setInstance(Vps_Component_Cache::CACHE_BACKEND_FNF);
        Vps_Component_Events_ViewCache::setModelClass('Vps_Component_Cache_Fnf_Model');
        Vps_Component_ModelObserver::getInstance()->clear();
        Vps_Component_ModelObserver::getInstance()->setSkipFnF(false);
        Vps_Media::getOutputCache()->clean();
    }

    public function tearDown()
    {
        Vps_Component_ModelObserver::getInstance()->clear();
        Vps_Component_ModelObserver::getInstance()->setSkipFnF(true);
        Vps_Component_Data_Root::reset();
        Vps_Component_Cache::clearInstance();
        Vps_Model_Abstract::clearInstances();
    }

    public static function assertValidHtml($uri)
    {
        if (!preg_match('#^[a-z]+://#', $uri)) {
            $uri = 'http://'.Vps_Registry::get('testDomain').$uri;
        }

        $validatorUrl = "http://vivid.vps/w3c-markup-validator/check?uri=".rawurlencode($uri);
        $client = new Zend_Http_Client($validatorUrl, array('timeout' => 20));
        $validator_response = $client->request();
        $status = $validator_response->getHeader('X-W3C-Validator-Status');
        $errors = $validator_response->getHeader('X-W3C-Validator-Errors');
        if ($status != 'Valid') {
            if ($errors) {
                self::fail("HTML Validation failed, validator reported $errors errors.\n See $validatorUrl");
            } else {
                self::fail("HTML Validation failed, validator din't errors.\n See $validatorUrl");
            }
        }
    }
}
