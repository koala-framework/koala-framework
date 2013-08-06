<?php
class Kwf_Component_ChainedNeedsParentCc_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_ChainedNeedsParentCc_Root_Component');
    }

    public function testMaster()
    {
        $cc = Kwc_Abstract::getChildComponentClasses('Kwf_Component_ChainedNeedsParentCc_Master_Component');
        $pc = Kwc_Abstract::getSetting($cc[0], 'parentComponentClass');
        $this->assertEquals($pc, 'Kwf_Component_ChainedNeedsParentCc_Master_Component');
    }

    public function testSlave()
    {
        $cc = Kwc_Abstract::getChildComponentClasses('Kwf_Component_ChainedNeedsParentCc_Chained_Component.Kwf_Component_ChainedNeedsParentCc_Master_Component');
        $m = Kwc_Abstract::getSetting($cc[0], 'masterComponentClass');
        $pc = Kwc_Abstract::getSetting($m, 'parentComponentClass');
        $this->assertEquals($pc, 'Kwf_Component_ChainedNeedsParentCc_Master_Component');
    }
}
