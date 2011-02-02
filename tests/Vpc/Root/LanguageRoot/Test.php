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
        $this->_root->setFilename(null);
    }

    public function testIt()
    {
        $domain = Zend_Registry::get('config')->server->domain;

        $data = $this->_root->getPageByUrl('http://'.$domain.'/', 'de');
        $this->assertEquals('1', $data->componentId);
        $this->assertEquals('/de', $data->url);

        $data = $this->_root->getPageByUrl('http://'.$domain.'/de', 'en');
        $this->assertEquals($data->componentId, '1');

        $data = $this->_root->getPageByUrl('http://'.$domain.'/de', ''); //erste
        $this->assertEquals($data->componentId, '1');

        $data = $this->_root->getPageByUrl('http://'.$domain.'/', 'en');
        $this->assertEquals($data->componentId, '3');
        $this->assertEquals($data->url, '/en');

        $data = $this->_root->getPageByUrl('http://'.$domain.'/en', 'de');
        $this->assertEquals($data->componentId, '3');

        $data = $this->_root->getPageByUrl('http://'.$domain.'/en', 'en');
        $this->assertEquals($data->componentId, '3');


        $data = $this->_root->getPageByUrl('http://'.$domain.'/', 'fr');
        $this->assertEquals($data->componentId, '5');
        $this->assertEquals($data->url, '/fr');

        $data = $this->_root->getPageByUrl('http://'.$domain.'/fr', 'en');
        $this->assertEquals($data->componentId, '5');
    }
}
