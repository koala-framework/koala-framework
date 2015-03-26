<?php
/**
 * @group Kwc_Trl
 *
 */
class Kwc_Trl_PagesPlusTable_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_PagesPlusTable_Root');
    }

    public function testComponentCountsMaster()
    {
        $c = $this->_root->getComponentsByClass('Kwc_Trl_PagesPlusTable_TestComponent_Component');
        $this->assertEquals(2, count($c));

        $c = $this->_root->getComponentsByClass('Kwc_Trl_PagesPlusTable_TestTableComponent_Component');
        $this->assertEquals(1, count($c));

        $c = $this->_root->getComponentsByClass('Kwc_Trl_PagesPlusTable_TestTableComponent_Child_Component');
        $this->assertEquals(2, count($c));
    }

    public function testComponentCountsTrl()
    {
        $c = $this->_root->getComponentsByClass('Kwc_Chained_Trl_Component.Kwc_Trl_PagesPlusTable_TestComponent_Component');
        $this->assertEquals(2*2, count($c));

        $c = $this->_root->getComponentsByClass('Kwc_Chained_Trl_Component.Kwc_Trl_PagesPlusTable_TestTableComponent_Component');
        $this->assertEquals(1*2, count($c));

        $c = $this->_root->getComponentsByClass('Kwc_Chained_Trl_Component.Kwc_Trl_PagesPlusTable_TestTableComponent_Child_Component');
        $this->assertEquals(3*2, count($c));
    }

    public function testComponentByIdTrl1()
    {
        $c = $this->_root->getComponentById('root-en-main_1');
        $this->assertEquals($c->componentId, 'root-en-main_1');
        $this->assertEquals($c->componentClass, 'Kwc_Chained_Trl_Component.Kwc_Trl_PagesPlusTable_TestTableComponent_Component');
    }

    public function testComponentByIdTrl2()
    {
        $c = $this->_root->getComponentById('root-en-main_1_1');
        $this->assertEquals($c->componentId, 'root-en-main_1_1');
        $this->assertEquals($c->componentClass, 'Kwc_Chained_Trl_Component.Kwc_Trl_PagesPlusTable_TestTableComponent_Child_Component');
    }

    public function testComponentByIdTrl3()
    {
        $c = $this->_root->getComponentById('root-en-main_1_2');
        $this->assertEquals($c->componentId, 'root-en-main_1_2');
        $this->assertEquals($c->componentClass, 'Kwc_Chained_Trl_Component.Kwc_Trl_PagesPlusTable_TestTableComponent_Child_Component');
    }

    public function testComponentByIdTrl4()
    {
        $c = $this->_root->getComponentById('root-en-main_2');
        $this->assertEquals($c->componentId, 'root-en-main_2');
        $this->assertEquals($c->componentClass, 'Kwc_Chained_Trl_Component.Kwc_Trl_PagesPlusTable_TestComponent_Component');
    }

    public function testComponentByIdTrl5()
    {
        $c = $this->_root->getComponentById('root-en-main_2_1');
        $this->assertNull($c);
    }

    public function testComponentByIdTrl6()
    {
        $c = $this->_root->getComponentById('root-en-main_2_2');
        $this->assertNull($c);
    }

    public function testComponentByIdTrl7()
    {
        $c = $this->_root->getComponentsByClass('Kwc_Chained_Trl_Component.Kwc_Trl_PagesPlusTable_TestTableComponent_Child_Component', array('ignoreVisible'=>true));
        $this->assertEquals(count($c), 3*2);

        $c = $this->_root->getComponentById('root-en-main_1', array('ignoreVisible'=>true));
        $this->assertEquals($c->componentId, 'root-en-main_1');
        $this->assertEquals($c->componentClass, 'Kwc_Chained_Trl_Component.Kwc_Trl_PagesPlusTable_TestTableComponent_Component');

        $childPages = $c->getChildPages();
        $this->assertEquals(count($childPages), 3+2);
    }
}
