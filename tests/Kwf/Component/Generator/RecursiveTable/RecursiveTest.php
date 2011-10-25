<?php
/**
 * @group Generator_RecursiveTable
 */
class Kwf_Component_Generator_RecursiveTable_RecursiveTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_RecursiveTable_Root');
    }

    public function testPages()
    {
        $c = $this->_root->getChildComponent('-1')->getChildComponents();
        $this->assertEquals(count($c), 1);

        $c = $this->_root->getChildComponent('-1')
            ->getChildComponents(array('filename' => 'bar'));
        $this->assertEquals(count($c), 1);

        $c = $this->_root->getChildComponent('-1');
        Kwf_Debug::enable();
        $c = $c->getChildComponents(array('filename' => 'bar'));
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 'root-1_1');

        $c = $this->_root
            ->getRecursiveChildComponents(array('filename' => 'bar'));
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 'root-1_1');
    }

    public function testFlag()
    {
        $c = $this->_root->getRecursiveChildComponents(array('flag' => 'testFlag'));
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 'root-static');

        $c = $this->_root->getChildComponent('-1')
            ->getRecursiveChildComponents(array('flag' => 'testFlag'));
        $this->assertEquals(count($c), 0);
    }
}
