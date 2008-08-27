<?php
class Vps_Component_Generator_Inherit_InheritTest extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Registry::get('config')->vpc->rootComponent = 'Vps_Component_Generator_Inherit_Root';
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testInherit()
    {
        $c = $this->_root->getChildComponent('_static')->getChildComponents();
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 'root_static-box');

        $c = $this->_root->getChildComponent('_static')->getChildBoxes();
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 'root_static-box');

        $this->markTestIncomplete();
        return;

        //buggy weil das nicht statisch herausgefunden werden kann - same problem as before
        $c = $this->_root->getChildComponent('_static')
            ->getRecursiveChildComponents(array('flags'=>array('foo'=>true)));
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 'root_static-box-flag');

    }

}
