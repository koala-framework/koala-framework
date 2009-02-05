<?php
/**
 * @group Generator_GetChildIds
 */
class Vps_Component_Generator_GetChildIds_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_GetChildIds_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testByClass()
    {
        $this->assertEquals(array(1, 2), $this->_root->getChildIds(array('generator' => 'table')));
    }
}
