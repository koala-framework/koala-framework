<?php
/**
 * @group DependingOnRows
 */
class Vps_Component_DependingOnRows_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_DependingOnRows_Root');
    }

    public function testSimple()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Component_DependingOnRows_PagesModel');
        $c = $m->getRow(2)->getComponentsDependingOnRow();
        $this->assertEquals(1, count($c));
        $this->assertEquals(10, $c[0]->componentId);
    }

    public function testParentPage()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Component_DependingOnRows_PagesModel');
        $c = $m->getRow(1)->getComponentsDependingOnRow();
        $this->assertEquals(1, count($c));
        $this->assertEquals(10, $c[0]->componentId);
    }

    public function testLinksToSelf()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Component_DependingOnRows_PagesModel');
        $c = $m->getRow(20)->getComponentsDependingOnRow();
        $this->assertEquals(0, count($c));
    }

    public function testLinksToChildren()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Component_DependingOnRows_PagesModel');
        $c = $m->getRow(30)->getComponentsDependingOnRow();
        $this->assertEquals(0, count($c));
    }
}
