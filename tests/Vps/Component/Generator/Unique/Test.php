<?php
class Vps_Component_Generator_Unique_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_Unique_Root');
    }

    public function testUnique()
    {

        $p = $this->_root->getComponentById('root_page2');
        $this->assertNotNull($p);
        $this->assertEquals(array_keys($p->getChildComponents()), array('root_page2-box2', 'root_page2_page3'));

        $p = $this->_root->getComponentById('root_page2_page3');
        $this->assertNotNull($p);
        $this->assertEquals(array_keys($p->getChildComponents()), array('root_page2_page3_page4', 'root_page2-box2'));

        $p = $this->_root->getComponentById('root_page2_page3_page4');
        $this->assertNotNull($p);
        $this->assertEquals(array_keys($p->getChildComponents()), array('root_page2-box2'));

        $p = $this->_root->getComponentById('1');
        $this->assertNotNull($p);
        $this->assertEquals(array_keys($p->getChildComponents()), array('1-pbox', '2'));

        $p = $this->_root->getComponentById('2');
        $this->assertNotNull($p);
        $this->assertEquals(array_keys($p->getChildComponents()), array('2_page3', '1-pbox'));

        $p = $this->_root->getComponentById('2_page3');
        $this->assertNotNull($p);
        $this->assertEquals(array_keys($p->getChildComponents()), array('1-pbox'));
    }

    //testet nicht das unique zeug direkt sondern nur einen speziellen bug
    //den ich damit hatte
    public function testUniqueRecursive()
    {
        $boxes = $this->_root->getChildComponents(array('box'=>true));
        $this->_assertIds($boxes, array('root-box'));

        $pages = array_values($this->_root->getChildPages());
        $this->_assertIds($pages, array('1', 'root_page2', 'root-box_page'));
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
