<?php
/**
 * @group Generator_Categories
 * @group Kwc_UrlResolve
 */
class Kwf_Component_Generator_Categories_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_Categories_Root');
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

        $this->assertEquals(3, count($this->_root->getComponentsByClass('Kwc_Root_Category_Component')));
        $this->assertNotNull($this->_root->getComponentByClass('Kwc_Root_Category_Component',
                                        array('id' => '-main')));
    }

    public function testPages()
    {
        $main = $this->_root->getChildComponent('-main');
        $this->assertEquals(2, count($main->getChildComponents()));
        $this->assertEquals(1, $main->getChildComponent()->componentId);
        $this->assertEquals('Kwc_Basic_None_Component', $main->getChildComponent()->componentClass);
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
        $model = new Kwf_Component_Model();
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

    public function testDuplicateHome()
    {
        $source = $this->_root->getComponentById('1');
        $target = $this->_root->getComponentById('4');
        $this->assertEquals(0, count($target->getChildPages()));

        Kwf_Events_ModelObserver::getInstance()->disable(); //PagesController also does that (for performance reasons)
        Kwf_Util_Component::duplicate($source, $target);
        Kwf_Events_ModelObserver::getInstance()->enable();

        $new = $this->_root->getComponentById('5');
        $this->assertEquals(false, $new->isHome);
    }

    public function testDuplicate()
    {
        $source = $this->_root->getComponentById('1');
        $target = $this->_root->getComponentById('4');
        $this->assertEquals(0, count($target->getChildPages()));

        Kwf_Events_ModelObserver::getInstance()->disable(); //PagesController also does that (for performance reasons)
        Kwf_Util_Component::duplicate($source, $target);
        Kwf_Events_ModelObserver::getInstance()->enable();

        $this->assertEquals(1, count($target->getChildPages()));
        $this->assertEquals(1, count($target->getChildPage()->getChildPages()));
    }

    public function testByFileName()
    {
        $pm = Kwf_Model_Abstract::getInstance('Kwf_Component_Generator_Categories_PagesModel');
        $c = $this->_root->getRecursiveChildComponent(array(
            'filename' => 'foo3',
            'pseudoPage'=>true,
        ));
        $this->assertEquals(4, $c->componentId);
    }

    public function testByFileNameCache()
    {
        $c = $this->_root->getChildComponent('-bottom')->getRecursiveChildComponent(array(
            'filename' => 'foo4',
            'pseudoPage'=>true,
        ));
        $this->assertEquals(null, $c);
        $pm = Kwf_Model_Abstract::getInstance('Kwf_Component_Generator_Categories_PagesModel');
        $pm->createRow(array('id'=>5, 'pos'=>3, 'visible'=>true, 'name'=>'Foo4', 'filename' => 'foo4', 'custom_filename' => false,
                'parent_id'=>'root-bottom', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'))
            ->save();
        $this->_process();
        $c = $this->_root->getChildComponent('-bottom')->getRecursiveChildComponent(array(
            'filename' => 'foo4',
            'pseudoPage'=>true,
        ));
        $this->assertNotNull($c);
        $this->assertEquals(5, $c->componentId);
    }

    public function testByFileNameCacheDirectyFromRoot()
    {
        $c = $this->_root->getRecursiveChildComponent(array(
            'filename' => 'foo4',
            'pseudoPage'=>true,
        ));
        $this->assertEquals(null, $c);
        $pm = Kwf_Model_Abstract::getInstance('Kwf_Component_Generator_Categories_PagesModel');
        $pm->createRow(array('id'=>5, 'pos'=>3, 'visible'=>true, 'name'=>'Foo4', 'filename' => 'foo4', 'custom_filename' => false,
                'parent_id'=>'root-bottom', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'))
            ->save();
        $this->_process();
        $c = $this->_root->getRecursiveChildComponent(array(
            'filename' => 'foo4',
            'pseudoPage'=>true,
        ));
        $this->assertNotNull($c);
        $this->assertEquals(5, $c->componentId);
    }

    public function testFileNameChangeCache()
    {
        $c = $this->_root->getChildComponent('-bottom')->getRecursiveChildComponent(array(
            'filename' => 'foo3',
            'pseudoPage'=>true,
        ));
        $this->assertNotNull($c);

        $pm = Kwf_Model_Abstract::getInstance('Kwf_Component_Generator_Categories_PagesModel');
        $row = $pm->getRow(4);
        $row->filename = 'bar';
        $row->save();
        $this->_process();

        $c = $this->_root->getChildComponent('-bottom')->getRecursiveChildComponent(array(
            'filename' => 'foo3',
            'pseudoPage'=>true,
        ));
        $this->assertNull($c);

        $row->filename = 'foo3';
        $row->save();
        $this->_process();

        $c = $this->_root->getChildComponent('-bottom')->getRecursiveChildComponent(array(
            'filename' => 'foo3',
            'pseudoPage'=>true,
        ));
        $this->assertNotNull($c);
    }
}
