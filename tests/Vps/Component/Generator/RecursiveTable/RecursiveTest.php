<?php
/**
 * @group Generator_RecursiveTable
 */
class Vps_Component_Generator_RecursiveTable_RecursiveTest extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_RecursiveTable_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testPages()
    {
        $c = $this->_root->getChildComponent('-1')
            ->getRecursiveChildComponents(array('filename' => 'bar'));
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 'root-1_foo');

        $c = $this->_root
            ->getRecursiveChildComponents(array('filename' => 'bar'));
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 'root-1_foo');
    }
}
