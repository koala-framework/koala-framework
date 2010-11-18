<?php
/**
 * @group Generator_RecursiveTable
 * Test der Nachbaut: Paragraphs wo Columns wo Paragraphs drin sind
 */
class Vps_Component_Generator_RecursiveTable2_RecursiveTest extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_RecursiveTable2_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testFlag2()
    {
        $c = $this->_root->getChildComponent('_page')
            ->getRecursiveChildComponents(array('flag' => 'testFlag'));
        $this->assertEquals(count($c), 1);
        $this->assertEquals(current($c)->componentId, 'root_page-2');

        $x = $this->_root->getChildComponent('_page')
            ->getChildComponent('-1');
        $c = $x->getRecursiveChildComponents(array('flag' => 'testFlag'));
        $this->assertEquals(count($c), 0);

        $c = $this->_root->getChildComponent('_page')
            ->getChildComponent('-1')
            ->getChildComponent('-3')
            ->getRecursiveChildComponents(array('flag' => 'testFlag'));
        $this->assertEquals(count($c), 0);
    }
}
