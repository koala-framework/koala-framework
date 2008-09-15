<?php
class Vps_Component_Generator_Priority_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_Priority_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }
    
    public function testBox1()
    {
        $boxes = $this->_root->getChildComponents(array('box'=>true));
        $this->_assertIds($boxes, array('root-foo'));
    }
    
    public function testBox2()
    {
        //deaktiviert weil sich vererbung geändert hat
        $this->markTestIncomplete();
        $boxes = $this->_root->getChildComponent('_page1')
                        ->getChildComponents(array('box'=>true));
        $ids = array_keys($boxes);
        $this->_assertIds($boxes, array('root_page1-box2'));
    }

    public function testBox3()
    {
        //deaktiviert weil sich vererbung geändert hat
        $this->markTestIncomplete();
        $boxes = $this->_root->getChildComponent('_page1')
                        ->getChildComponent('_page2')
                        ->getChildComponents(array('box'=>true));
        $this->_assertIds($boxes, array('root_page1_page2-box2'));
    }


    public function testBox4()
    {
        //deaktiviert weil sich vererbung geändert hat
        $this->markTestIncomplete();
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
    

    private function _assertIds($components, $ids)
    {
        $i = array();
        foreach ($components as $c) {
            $i[] = $c->componentId;
        }
        $this->assertEquals($i, $ids);
    }

}
