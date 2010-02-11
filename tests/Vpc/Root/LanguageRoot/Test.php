<?php
/**
 * @group Vpc_LanguageRoot
 * @group Vpc_UrlResolve
 */
class Vpc_Root_LanguageRoot_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Root_LanguageRoot_TestComponent');
    }

    public function testIt()
    {
        $data = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/', 'de');
        $this->assertEquals('1', $data->componentId);
        $this->assertEquals('/de', $data->url);

        $data = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/de', 'en');
        $this->assertEquals($data->componentId, '1');

        $data = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/de', ''); //erste
        $this->assertEquals($data->componentId, '1');

        $data = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/', 'en');
        $this->assertEquals($data->componentId, '3');
        $this->assertEquals($data->url, '/en');

        $data = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/en', 'de');
        $this->assertEquals($data->componentId, '3');

        $data = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/en', 'en');
        $this->assertEquals($data->componentId, '3');


        $data = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/', 'fr');
        $this->assertEquals($data->componentId, '5');
        $this->assertEquals($data->url, '/fr');

        $data = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/fr', 'en');
        $this->assertEquals($data->componentId, '5');
    }
}
