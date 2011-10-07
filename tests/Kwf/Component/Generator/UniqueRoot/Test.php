<?php
class Vps_Component_Generator_UniqueRoot_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_UniqueRoot_Root');
    }

    public function testUniqueRoot()
    {

        $p = $this->_root->getComponentById('root_page2');
        $this->assertNotNull($p);
        $this->assertEquals(array_keys($p->getChildComponents()), array('root-box'));
    }
}
