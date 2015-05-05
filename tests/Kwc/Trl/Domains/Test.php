<?php
/**
 * @group Kwc_Trl
 *
 */
class Kwc_Trl_Domains_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_Domains_Root');
    }

    public function testDomains()
    {
        $c = $this->_root->getChildComponents(array('componentClass'=>'Kwc_Trl_Domains_Domain_Component'));
        $this->assertEquals(3, count($c));
    }

    public function testLanguages()
    {
        $c = $this->_root->getChildComponent('-at')
            ->getChildComponents();
        $this->assertEquals(3, count($c));

        $c = $this->_root->getChildComponent('-hu')
            ->getChildComponents();
        $this->assertEquals(2, count($c));

        $c = $this->_root->getChildComponent('-ro')
            ->getChildComponents();
        $this->assertEquals(2, count($c));
    }

    public function testByClassMaster()
    {
        $c = $this->_root->getComponentsByClass('Kwc_Trl_Domains_TestComponent_Component');
        $this->assertEquals(3*3, count($c));
    }

    public function testByClassTrl()
    {
        $c = $this->_root->getComponentsByClass('Kwc_Chained_Trl_Component.Kwc_Trl_Domains_TestComponent_Component');
        $this->assertEquals(3*4, count($c));
    }
}
