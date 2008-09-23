<?php
class Vps_Component_Generator_IgnoreVisible_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_IgnoreVisible_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testStatic()
    {
        $c = $this->_root->getComponentById('root-child');
        $this->assertNotNull($c);
        $this->assertEquals('root-child', $c->componentId);

        $generators = Vps_Component_Generator_Abstract::getInstances($this->_root->componentClass);
        $this->assertEquals(count($generators), 1);

        $generators = Vps_Component_Generator_Abstract::getInstances($this->_root->componentClass,
                array('ignoreVisible' => false));
        $this->assertEquals(count($generators), 1);

        $generators = Vps_Component_Generator_Abstract::getInstances($this->_root->componentClass,
                array('ignoreVisible' => true));
        $this->assertEquals(count($generators), 1);

        $c = $this->_root->getComponentById('root-child', array('ignoreVisible'=>false));
        $this->assertNotNull($c);

        $c = $this->_root->getComponentById('root-child', array('ignoreVisible'=>true));
        $this->assertNotNull($c);
    }

    public function testTable()
    {
        $c = $this->_root->getComponentById('root-child_1');
        $this->assertNotNull($c);
        $this->assertEquals('root-child_1', $c->componentId);

        $c = $this->_root->getComponentById('root-child_1', array('ignoreVisible'=>false));
        $this->assertNotNull($c);

        $c = $this->_root->getComponentById('root-child_1', array('ignoreVisible'=>true));
        $this->assertNotNull($c);
    }
}
