<?php
/**
 * @group DependingOnRows
 */
class Vps_Component_DependingOnRows_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_DependingOnRows_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
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
