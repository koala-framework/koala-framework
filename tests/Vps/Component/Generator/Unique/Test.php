<?php
class Vps_Component_Generator_Unique_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_Unique_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testUnique()
    {
        $p = $this->_root->getComponentById('root_page2');
        $this->assertNotNull($p);
        $this->assertEquals(array_keys($p->getChildComponents()), array('root_page2-box2', 'root_page2_page3'));

        $p = $this->_root->getComponentById('root_page2_page3');
        $this->assertNotNull($p);
        $this->assertEquals(array_keys($p->getChildComponents()), array('root_page2-box2'));
    }

    //testet nicht das unique zeug direkt sondern nur einen speziellen bug
    //den ich damit hatte
    public function testUniqueRecursive()
    {
        $boxes = $this->_root->getChildComponents(array('box'=>true));
        $this->_assertIds($boxes, array('root-box'));

        $pages = array_values($this->_root->getChildPages());
        $this->_assertIds($pages, array('root_page2', 'root-box_page'));
        $page = $this->_root->getComponentById('root-box_page');
        $this->_assertIds($page->getChildComponents(), array('root-box_page-box2'));
        $this->_assertIds($page->getChildPages(), array());

    }


    private function _assertIds($components, $ids)
    {
        $i = array();
        foreach ($components as $c) {
            $i[] = $c->componentId;
        }
        $this->assertEquals($i, $ids);
    }

}
