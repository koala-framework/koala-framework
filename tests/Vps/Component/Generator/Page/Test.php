<?php
/**
 * @group Vpc_UrlResolve
 */
class Vps_Component_Generator_Page_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_Page_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testChilds()
    {
        $c = $this->_root->getChildPages(array('showInMenu'=>true));
        $this->assertEquals(count($c), 1);

        $c = $this->_root->getChildPages();
        $this->assertEquals(count($c), 1);

        $c = current($c)->getChildPages();
        $this->assertEquals(count($c), 1);

        $c = current($c)->getChildPages();
        $this->assertEquals(count($c), 2);
    }

    public function testSkipRoot()
    {
        $this->assertEquals(count($this->_root->getComponentById(2)
                                    ->getChildPages()), 2);

    }

    public function testBox()
    {
        $box = $this->_root->getComponentById(3)->getChildComponent('-title');
        $this->assertEquals('3-title', $box->componentId);
    }

    public function testGetGeneratorPage()
    {
        $pages = $this->_root->getComponentById(1)->getChildComponents(array('generatorPage' => true, 'page' => true));
        $this->assertEquals('2', current($pages)->componentId);
    }

    public function testFilename()
    {
        $ccc = Vpc_Abstract::getChildComponentClasses('Vps_Component_Generator_Page_Root', array('filename' => 'home'));
        $this->assertEquals(2, count($ccc));
        $this->assertEquals('Vpc_Basic_Empty_Component', current($ccc));
        $children = $this->_root->getChildComponents(array('filename' => 'home'));
        $this->assertEquals(1, count($children));
        $child = $this->_root->getChildComponent(array('filename' => 'home'));
        $this->assertEquals('1', $child->componentId);
        $child = $child->getChildComponent(array('filename' => 'foo'));
        $this->assertEquals('2', $child->componentId);
    }

    public function testHome()
    {
        $domain = 'http://'.Zend_Registry::get('config')->server->domain;
        $home = $this->_root->getPageByUrl($domain.'/', null);
        $this->assertNotNull($home);
        $this->assertEquals($home->url, '/');
        $child = $home->getChildComponent('-foo');
        $this->assertNotNull($child);
        $this->assertEquals($child->url, '/');

        $page = $this->_root->getComponentById('2');
        $this->assertNotNull($page);
        $this->assertEquals($page->url, '/home/foo');
    }

    public function testIdAndComponentClassConstraints()
    {
        $c = $this->_root->getComponentById(2, array('componentClass'=>'Vpc_Basic_Empty_Component'));
        $this->assertNotNull($c);

        $c = $this->_root->getComponentById(2, array('componentClass'=>'Vps_Component_Generator_Page_Child'));
        $this->assertNull($c);
    }
}
