<?php
/**
 * @group Generator_UniqueLevel
 */
class Vps_Component_Generator_UniqueLevel_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_UniqueLevel_Root');
    }

    public function testUniqueLevel()
    {
        $p = $this->_root->getComponentById('1');
        $this->assertNotNull($p);
        $this->assertEquals(array('1-menu', '2'), array_keys($p->getChildComponents()));

        $p = $this->_root->getComponentById('2');
        $this->assertNotNull($p);
        $this->assertEquals(array('2-menu', '3'), array_keys($p->getChildComponents()));

        $p = $this->_root->getComponentById('3');
        $this->assertNotNull($p);
        $this->assertEquals(array('2-menu'), array_keys($p->getChildComponents()));

        $p = $this->_root->getComponentById('1-menu');
        $this->assertNotNull($p);
        $this->assertEquals(array('1-menu-menu'), array_keys($p->getChildComponents()));

        $p = $this->_root->getComponentById('2-menu');
        $this->assertNotNull($p);
        $this->assertEquals(array('2-menu-menu'), array_keys($p->getChildComponents()));

        $p = $this->_root->getComponentById('3-menu');
        $this->assertNotNull($p);
        $this->assertEquals(array('2-menu-menu'), array_keys($p->getChildComponents()));
    }
}
