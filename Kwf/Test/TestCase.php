<?php
class Kwf_Test_TestCase extends PHPUnit_Framework_TestCase
{
    protected $backupStaticAttributes = false;

    public function setUp()
    {
        Kwf_Component_Data_Root::setComponentClass(false);
        Kwf_Component_Cache::setInstance(Kwf_Component_Cache::CACHE_BACKEND_FNF);
        Kwf_Component_ModelObserver::getInstance()->setSkipFnF(false);
        Kwf_Media::getOutputCache()->clean();
    }

    public function tearDown()
    {
        Kwf_Component_ModelObserver::getInstance()->setSkipFnF(true);
        Kwf_Component_Data_Root::reset();
        Kwf_Component_Cache::clearInstance();
        Kwf_Model_Abstract::clearInstances();
    }

    public static function assertValidHtml($uri)
    {
        if (!preg_match('#^[a-z]+://#', $uri)) {
            $uri = 'http://'.Kwf_Registry::get('testDomain').$uri;
        }

        $validatorUrl = "http://vivid.kwf/w3c-markup-validator/check?uri=".rawurlencode($uri);
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
