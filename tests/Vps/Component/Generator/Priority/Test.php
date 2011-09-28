<?php
class Vps_Component_Generator_Priority_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_Priority_Root');
    }

    public function testBox1()
    {
        $boxes = $this->_root->getChildComponents(array('box'=>true));
        $this->_assertIds($boxes, array('root-foo'));
    }

    public function testBox2()
    {
        $boxes = $this->_root->getChildComponent('_page1')
                        ->getChildComponents(array('box'=>true));
        $ids = array_keys($boxes);
        $this->_assertIds($boxes, array('root_page1-box2'));
    }

    public function testBox3()
    {
        $boxes = $this->_root->getChildComponent('_page1')
                        ->getChildComponent('_page2')
                        ->getChildComponents(array('box'=>true));
        $this->_assertIds($boxes, array('root_page1_page2-box2'));
    }


    public function testBox4()
    {
        $boxes = $this->_root->getChildComponent('_page1')
                        ->getChildComponent('_page3')
                        ->getChildComponents(array('box'=>true));
        $this->_assertIds($boxes, array('root_page1_page3-box4'));

        $boxes = $this->_root->getChildComponent('_page1')
                        ->getChildComponent('_page3')
                        ->getChildComponent('_page4')
                        ->getChildComponents(array('box'=>true));
        $this->_assertIds($boxes, array('root_page1_page3_page4-box2'));
    }

    public function testSamePriorityBox()
    {
        $boxes = $this->_root->getChildComponent('_page1')
                        ->getChildComponent('_page3')
                        ->getChildComponent('_page5')
                        ->getChildComponents(array('box'=>true));
        $this->_assertIds($boxes, array('root_page1_page3_page5-box5'));
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
