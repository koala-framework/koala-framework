<?php
/**
 * @group Generator_Categories
 * @group Vpc_UrlResolve
 */
class Vps_Component_Generator_Categories_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_Categories_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testCategories()
    {
        $categories = $this->_root->getChildComponents();
        $this->assertEquals(4, count($categories));

        $category = $this->_root->getChildComponent('-main');
        $this->assertEquals('root-main', $category->componentId);
        $this->assertEquals('root', $category->parent->componentId);
        $this->assertEquals('root-main', $category->dbId);
        $this->assertEquals('root', $category->parent->dbId);

        $this->assertNotNull($this->_root->getComponentById('root-main'));

        $this->assertEquals(3, count($this->_root->getComponentsByClass('Vpc_Root_Category_Component')));
        $this->assertNotNull($this->_root->getComponentByClass('Vpc_Root_Category_Component',
                                        array('id' => '-main')));
    }

    public function testPages()
    {
        $main = $this->_root->getChildComponent('-main');
        $this->assertEquals(2, count($main->getChildComponents()));
        $this->assertEquals(1, $main->getChildComponent()->componentId);
        $this->assertEquals('Vpc_Basic_Empty_Component', $main->getChildComponent()->componentClass);
        $this->assertEquals('root-main', $main->getChildComponent()->parent->componentId);
        $this->assertEquals(2, $main->getChildComponent()->getChildComponent()->componentId);
    }

    public function testById()
    {
        $this->assertNotNull($this->_root->getComponentById('1'));
        $this->assertEquals('root-main', $this->_root->getComponentById('1')->parent->componentId);
        $this->assertNotNull($this->_root->getComponentById('2'));
        $this->assertEquals('1', $this->_root->getComponentById('2')->parent->componentId);
    }

    public function testHome()
    {
        $this->assertEquals('1', $this->_root->getChildPage(array('home' => true))->componentId);
    }

    public function testByPath()
    {
        $domain = 'http://'.Zend_Registry::get('config')->server->domain;
        $this->assertEquals('1', $this->_root->getPageByUrl($domain.'/', null)->componentId);
        $this->assertEquals('2', $this->_root->getPageByUrl($domain.'/home/foo', null)->componentId);
        $this->assertEquals('4', $this->_root->getPageByUrl($domain.'/foo3', null)->componentId);
    }

    public function testTitle()
    {
        $c = $this->_root;
        $this->assertNotNull($c->getChildComponent('-title'));
        $c = $this->_root->getComponentById('1');
        $this->assertNotNull($c->getChildComponent('-title'));
    }

    public function testModel()
    {
        $model = new Vps_Component_Model();
        $model->setRoot($this->_root);

        $select = $model->select()->whereNull('parent_id');
        $this->assertEquals('root', $model->getRow($select)->componentId);
        $this->assertEquals(1, $model->countRows($select));

        $select = $model->select()->whereEquals('parent_id', 'root');
        $this->assertEquals('root-main', $model->getRow($select)->componentId);
        $this->assertEquals(3, $model->countRows($select));

        $select = $model->select()->whereEquals('parent_id', 'root-main');
        $this->assertEquals('1', $model->getRow($select)->componentId);
        $this->assertEquals(1, $model->countRows($select));
    }
}
