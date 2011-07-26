<?php
/**
 * @group Vpc_Trl
 *
ansicht frontend:
http://vps.niko.vivid/vps/vpctest/Vpc_Trl_Pages_Root/de
http://vps.niko.vivid/vps/vpctest/Vpc_Trl_Pages_Root/de/home_de/test
http://vps.niko.vivid/vps/vpctest/Vpc_Trl_Pages_Root/en
http://vps.niko.vivid/vps/vpctest/Vpc_Trl_Pages_Root/en/home_en/test
http://vps.niko.vivid/vps/vpctest/Vpc_Trl_Pages_Root/it
http://vps.niko.vivid/vps/vpctest/Vpc_Trl_Pages_Root/it/home_it/test


 */
class Vpc_Trl_Pages_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_Pages_Root');
    }
    public function testGetHomeIt()
    {
        $rootIt = Vps_Component_Data_Root::getInstance()->getComponentById('root-it');
        $ret = $rootIt->getChildPage(array('home' => true), array());
        $this->assertEquals('root-it-main_1', $ret->componentId);
    }
    public function testPages()
    {
        $domain = Zend_Registry::get('config')->server->domain;

        $c = $this->_root->getPageByUrl('http://'.$domain.'/de', 'en');
        $this->assertEquals($c->componentId, '1');
        $this->assertEquals($c->render(), 'test \'1\'');

        $c = $this->_root->getPageByUrl('http://'.$domain.'/de/home_de/test', 'en');
        $this->assertEquals($c->componentId, '2');
        $this->assertEquals($c->render(), 'test \'2\'');

        $c = $this->_root->getPageByUrl('http://'.$domain.'/en', 'en');
        $this->assertEquals($c->componentId, 'root-en-main_1');
        $this->assertEquals($c->render(), 'test \'root-en-main_1\'');

        $c = $this->_root->getPageByUrl('http://'.$domain.'/en/home_en/test', 'en');
        $this->assertEquals($c->componentId, 'root-en-main_2');
        $this->assertEquals($c->render(), 'test \'root-en-main_2\'');
    }

    public function testGetById()
    {
        $c = $this->_root->getComponentById('root-en-main_2');
        $this->assertEquals($c->componentId, 'root-en-main_2');

        $c = $this->_root->getComponentById('root-en-main_1');
        $this->assertEquals($c->componentId, 'root-en-main_1');
    }

    public function testChildPage()
    {
        $c = $this->_root->getComponentById('root-en-main_1');
        $this->assertEquals($c->getChildComponent(array('filename' => 'test'))->componentId, 'root-en-main_2');
    }

    public function testParentPage()
    {
        $c = $this->_root->getComponentById('root-en-main_2');
        $this->assertEquals($c->parent->componentId, 'root-en-main_1');
        $this->assertEquals($c->parent->parent->componentId, 'root-en-main');
    }

    public function testParentPage2()
    {
        $c = $this->_root->getComponentById('2');
        $this->assertEquals($c->parent->componentId, '1');
        $this->assertEquals($c->parent->parent->componentId, 'root-master-main');
    }

    public function testInvisibleInMaster()
    {
        $domain = Zend_Registry::get('config')->server->domain;

        //de ist nicht visible
        //$this->assertFalse(!!$this->_root->getComponentById('3'));
        //$this->assertFalse(!!$this->_root->getPageByUrl('http://'.$domain.'/de/home/test2', 'de'));

        //en ist visible
        //$this->assertTrue(!!$this->_root->getComponentById('root-en-main_3'));

        $c = $this->_root->getPageByUrl('http://'.$domain.'/en/home_en/test2_en', 'en');
        $this->assertEquals($c->componentId, 'root-en-main_3');
        $this->assertEquals($c->render(), 'test \'root-en-main_3\'');

    }
}
