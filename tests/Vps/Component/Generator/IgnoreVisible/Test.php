<?php
class Vps_Component_Generator_IgnoreVisible_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_IgnoreVisible_Root');
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

        $c = $this->_root->getComponentById('root-child_2');
        $this->assertNull($c);

        $c = $this->_root->getComponentById('root-child_2', array('ignoreVisible'=>true));
        $this->assertNotNull($c);

        $c = $this->_root->getComponentById('root-child');
        $this->assertEquals(array_keys($c->getChildComponents()), array('root-child_1'));
        $this->assertEquals(array_keys($c->getChildComponents(array('ignoreVisible'=>false))),
                array('root-child_1'));
        $this->assertEquals(array_keys($c->getChildComponents(array('ignoreVisible'=>true))),
                array('root-child_1', 'root-child_2', 'root-child_3'));
        $this->assertNotNull($c);
    }

    public function testStaticInInvisibleTable()
    {
        $c = $this->_root->getComponentById('root-child_1-bar');
        $this->assertNotNull($c);
        $this->assertEquals('root-child_1-bar', $c->componentId);

        $c = $this->_root->getComponentById('root-child_2-bar');
        $this->assertNull($c);

        $c = $this->_root->getComponentById('root-child_2-bar', array('ignoreVisible'=>true));
        $this->assertNotNull($c);
        $this->assertEquals('root-child_2-bar', $c->componentId);
    }
}
