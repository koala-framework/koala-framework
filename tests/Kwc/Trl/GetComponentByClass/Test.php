<?php
/**
 * @group 
 */
class Kwc_Trl_GetComponentByClass_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_GetComponentByClass_Root');
    }

    public function testDe()
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentsByClass('Kwc_Basic_None_Component');
        $this->assertEquals(1, count($c));
        $this->assertEquals('root-master_test1', $c[0]->componentId);
    }

    public function testEn()
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentsByClass('Kwc_Chained_Trl_Component.Kwc_Basic_None_Component');
        $this->assertEquals(1, count($c));
        $this->assertEquals('root-en_test1', $c[0]->componentId);
    }

    public function testGetComponentClassesByParentClass()
    {
        $c = 'Kwc_Chained_Trl_Component.Kwc_Basic_None_Component';
        $cls = Kwc_Abstract::getComponentClassesByParentClass($c);
        $this->assertEquals(array($c), $cls);

        $c = 'Kwc_Chained_Trl_Component.Kwc_Basic_None_Component';
        $cls = Kwc_Abstract::getComponentClassesByParentClass('Kwc_Chained_Trl_Component');
        $this->assertEquals(array($c), $cls);
    }
}
