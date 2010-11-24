<?php
/**
 * @group Generator_RecursiveTable
 */
class Vps_Component_Generator_RecursiveTable_RecursiveTest extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_RecursiveTable_Root');
    }

    public function testPages()
    {
        $c = $this->_root->getChildComponent('-1')->getChildComponents();
        $this->assertEquals(count($c), 1);

        $c = $this->_root->getChildComponent('-1')
            ->getChildComponents(array('filename' => 'bar'));
        $this->assertEquals(count($c), 1);

        $c = $this->_root->getChildComponent('-1')
            ->getChildComponents(array('filename' => 'bar'));
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 'root-1_1');

        $c = $this->_root
            ->getRecursiveChildComponents(array('filename' => 'bar'));
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 'root-1_1');
    }
}
