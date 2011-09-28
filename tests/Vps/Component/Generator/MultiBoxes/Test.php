<?php
class Vps_Component_Generator_MultiBoxes_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_MultiBoxes_Root');
    }

    public function testBoxes()
    {
        $boxes = $this->_root->getChildComponents(array('box'=>true));
        $this->_assertIds($boxes, array('root-foo'));
    }

    public function testInheritedBoxes()
    {
        $page = $this->_root->getChildComponent('_page1');

        $generators = Vps_Component_Generator_Abstract::getInstances($page, array('box'=>true));
        $this->assertEquals(1, count($generators));

        $boxes = $page->getChildComponents(array('box'=>true));
        $this->_assertIds($boxes, array('root_page1-foo'));
    }

    public function testMultiBoxes()
    {
        $boxes = $this->_root->getChildComponents(array('multiBox'=>true));
        $this->_assertIds($boxes, array('root-multibox1', 'root-multibox2', 'root-multibox3'));
    }

    public function testInheritedMultiBoxes()
    {
        $boxes = $this->_root->getChildComponent('_page1')
                        ->getChildComponents(array('multiBox'=>true));
        $this->_assertIds($boxes, array('root_page1-multibox1', 'root_page1-multibox2',
                                        'root_page1-multibox3'));
    }


    public function testMultiboxVars()
    {
        $vars = $this->_root->getChildComponent('_page1')->getChildComponent('-foo')
                    ->getComponent()->getTemplateVars();

        $this->_assertIds($vars['boxes'], array('root_page1-multibox3', 'root_page1-multibox1', 'root_page1-multibox2'));
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
