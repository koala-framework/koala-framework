<?php
class Vps_Component_Generator_UniqueRoot_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_UniqueRoot_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testUniqueRoot()
    {

        $p = $this->_root->getComponentById('root_page2');
        $this->assertNotNull($p);
        $this->assertEquals(array_keys($p->getChildComponents()), array('root-box'));
    }
}
