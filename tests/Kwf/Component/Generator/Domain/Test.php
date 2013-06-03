<?php
/**
 * @group Generator_Domain
 * @group Kwc_UrlResolve
 */
class Kwf_Component_Generator_Domain_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_Domain_Root');
    }

    public function testDomains()
    {
        $components = $this->_root->getChildComponents();
        $this->assertEquals(3, count($components));

        $domain = $this->_root->getChildComponent('-at');
        $this->assertEquals('root-at', $domain->componentId);
        $this->assertEquals('root', $domain->parent->componentId);
        $this->assertEquals('root-at', $domain->dbId);
        $this->assertEquals('root', $domain->parent->dbId);

        $this->assertNotNull($this->_root->getComponentById('root-at'));
    }

    public function testCategories()
    {
        $at = $this->_root->getComponentById('root-at');
        $categories = $at->getChildComponents();
        $this->assertEquals(3, count($categories));

        $category = $at->getChildComponent('-main');
        $this->assertEquals('root-at-main', $category->componentId);
        $this->assertEquals('root-at', $category->parent->componentId);
        $this->assertEquals('root-at-main', $category->dbId);
        $this->assertEquals('root-at', $category->parent->dbId);

        $this->assertNotNull($this->_root->getComponentById('root-at-main'));
    }

    public function testPages()
    {
        $main = $this->_root->getChildComponent('-at')->getChildComponent('-main');
        $this->assertEquals(2, count($main->getChildComponents()));
        $c = $main->getChildComponent();
        $this->assertEquals(1, $c->componentId);
        $this->assertEquals('Kwc_Basic_None_Component', $c->componentClass);
        $this->assertEquals('root-at-main', $c->parent->componentId);
        $this->assertEquals(2, $c->getChildComponent()->componentId);
    }

    public function testById()
    {
        // at
        $page = $this->_root->getComponentById('1');
        $this->assertNotNull($page);
        $this->assertEquals(1, $page->componentId);
        $this->assertEquals('root-at-main', $page->parent->componentId);
        $this->assertNotNull($this->_root->getComponentById('2'));
        $this->assertEquals('Kwc_Basic_Link_Component', $this->_root->getComponentById('2')->componentClass);
        $this->assertEquals('1', $this->_root->getComponentById('2')->parent->componentId);
        $this->assertEquals('root-at-main', $this->_root->getComponentById('2')->parent->parent->componentId);

        // ch
        $page = $this->_root->getComponentById('5');
        $this->assertNotNull($page);
        $this->assertEquals(5, $page->componentId);
        $this->assertEquals('root-ch-main', $page->parent->componentId);
        $this->assertNotNull($this->_root->getComponentById('6'));
        $this->assertEquals('Kwc_Basic_None_Component', $this->_root->getComponentById('6')->componentClass);
        $this->assertEquals('5', $this->_root->getComponentById('6')->parent->componentId);
        $this->assertEquals('root-ch-main', $this->_root->getComponentById('6')->parent->parent->componentId);
    }

    public function testByFilename()
    {
        $ch = $this->_root->getComponentById('root-ch');
        $this->assertNotNull($ch);
        $home = $ch->getChildPseudoPage(array('filename' => 'home', 'recursive' => true));
        $this->assertNotNull($home);
        $this->assertNotNull($home->getChildPseudoPage(array('filename' => 'foo')));
        $this->assertEquals(1, count($ch->getChildComponent('-main')->getChildPseudoPages()));
        $this->assertEquals(2, count($ch->getChildPseudoPages()));
    }

    public function testChildPageByPath()
    {
        $this->assertEquals('root-ch', $this->_root->getChildPageByPath('ch')->componentId);
    }

    public function testByPath()
    {
//         $this->assertEquals('6', $this->_root->getPageByUrl('http://rotary.ch/home/foo?x=1', null)->componentId);
        $this->assertEquals('5', $this->_root->getPageByUrl('http://rotary.ch/', null)->componentId);
        $this->assertEquals('1', $this->_root->getPageByUrl('http://rotary.at/', null)->componentId);
        $this->assertEquals('2', $this->_root->getPageByUrl('http://rotary.at/home/foo?x=1', null)->componentId);
        $this->assertEquals('4', $this->_root->getPageByUrl('http://rotary.at/foo3', null)->componentId);
        $this->assertEquals('7', $this->_root->getPageByUrl('http://rotary.ch/foo3', null)->componentId);
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
        $this->assertEquals(1, $model->countRows($select));
        $this->assertEquals('root', $model->getRow($select)->componentId);

        $select = $model->select()->whereEquals('parent_id', 'root');
        $this->assertEquals(2, $model->countRows($select));
        $this->assertEquals('root-at', $model->getRow($select)->componentId);

        $select = $model->select()->whereEquals('parent_id', 'root-at');
        $this->assertEquals(2, $model->countRows($select));
        $this->assertEquals('root-at-main', $model->getRow($select)->componentId);

        $select = $model->select()->whereEquals('parent_id', 'root-at-main');
        $this->assertEquals(1, $model->countRows($select));
        $this->assertEquals('1', $model->getRow($select)->componentId);

        $select = $model->select()->whereEquals('parent_id', 'root-ch-main');
        $this->assertEquals(1, $model->countRows($select));
        $this->assertEquals('5', $model->getRow($select)->componentId);
    }

    public function testSubroot()
    {
        $components = $this->_root->getComponentsByClass('Kwc_Basic_Image_Component');
        $this->assertEquals(2, count($components));

        $components = $this->_root->getComponentsByClass('Kwc_Basic_Image_Component');
        $this->assertEquals(2, count($components));

        $c = $this->_root->getComponentById('6');
        $components = $this->_root->getComponentsByClass('Kwc_Basic_Image_Component', array('subroot' => $c));
        $this->assertEquals(1, count($components));

        $component = $this->_root->getComponentByClass('Kwc_Basic_Image_Component', array('subroot' => $c));
        $this->assertEquals(5, $component->componentId);
    }
}
