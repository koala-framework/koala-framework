<?php
/**
 * @group DependingOnRows
 */
class Kwf_Component_DependingOnRows_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_DependingOnRows_Root');
    }

    public function testSimple()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_DependingOnRows_PagesModel');
        $c = $m->getRow(2)->getComponentsDependingOnRow();
        $this->assertEquals(1, count($c));
        $this->assertEquals(10, $c[0]->componentId);
    }

    public function testParentPage()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_DependingOnRows_PagesModel');
        $c = $m->getRow(1)->getComponentsDependingOnRow();
        $this->assertEquals(1, count($c));
        $this->assertEquals(10, $c[0]->componentId);
    }

    public function testLinksToSelf()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_DependingOnRows_PagesModel');
        $c = $m->getRow(20)->getComponentsDependingOnRow();
        $this->assertEquals(0, count($c));
    }

    public function testLinksToChildren()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_DependingOnRows_PagesModel');
        $c = $m->getRow(30)->getComponentsDependingOnRow();
        $this->assertEquals(0, count($c));
    }
}
