<?php
/**
 * @group Vpc_Trl
 * @group Vpc_TrlRoot
 * @group Vpc_UrlResolve

http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Root_TrlRoot_TestComponent/de/test
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Root_TrlRoot_TestComponent/de/home_de
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Root_TrlRoot_TestComponent/de/test2
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Root_TrlRoot_TestComponent/de
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Root_TrlRoot_TestComponent/en/test
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Root_TrlRoot_TestComponent/en/test2_en
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Root_TrlRoot_TestComponent/en/home_de
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Root_TrlRoot_TestComponent/en
 */
class Vpc_Root_TrlRoot_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Root_TrlRoot_TestComponent');
        $this->_root->setFilename(null);
    }

    public function testIt()
    {
        $domain = Vps_Registry::get('config')->server->domain;

        $data = $this->_root->getPageByUrl('http://'.$domain.'/', 'de');
        $this->assertEquals('1', $data->componentId);
        $this->assertEquals('/de', $data->url);

        $data = $this->_root->getPageByUrl('http://'.$domain.'/', null);
        $this->assertEquals('1', $data->componentId);

        $data = $this->_root->getPageByUrl('http://'.$domain.'/de', 'de');
        $this->assertEquals('1', $data->componentId);

        $data = $this->_root->getPageByUrl('http://'.$domain.'/de', 'en');
        $this->assertEquals('1', $data->componentId);

        $data = $this->_root->getPageByUrl('http://'.$domain.'/de/test', 'de');
        $this->assertEquals('2', $data->componentId);
        $this->assertEquals('/de/test', $data->url);

        $data = $this->_root->getPageByUrl('http://'.$domain.'/de/test2', 'de');
        $this->assertEquals('3', $data->componentId);
        $this->assertEquals('/de/test2', $data->url);


        $data = $this->_root->getPageByUrl('http://'.$domain.'/', 'en');
        $this->assertEquals('root-en_1', $data->componentId);
        $this->assertEquals('/en', $data->url);

        $data = $this->_root->getPageByUrl('http://'.$domain.'/en', 'en');
        $this->assertEquals('root-en_1', $data->componentId);

        $data = $this->_root->getPageByUrl('http://'.$domain.'/en', 'de');
        $this->assertEquals('root-en_1', $data->componentId);

        $data = $this->_root->getPageByUrl('http://'.$domain.'/', 'en');
        $this->assertEquals('root-en_1', $data->componentId);
        $this->assertEquals('/en', $data->url);

        $data = $this->_root->getPageByUrl('http://'.$domain.'/', 'en');
        $this->assertEquals('root-en_1', $data->componentId);
        $this->assertEquals('/en', $data->url);

        $data = $this->_root->getPageByUrl('http://'.$domain.'/en/test', 'en');
        $this->assertEquals('root-en_2', $data->componentId);
        $this->assertEquals('/en/test', $data->url);

        $data = $this->_root->getPageByUrl('http://'.$domain.'/en/test2_en', 'en');
        $this->assertEquals('root-en_3', $data->componentId);
        $this->assertEquals('/en/test2_en', $data->url);
    }
}
