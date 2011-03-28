<?php
/**
 * @group 
 */
class Vpc_Trl_GetComponentByClass_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_GetComponentByClass_Root');
    }

    public function testDe()
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentsByClass('Vpc_Basic_Empty_Component');
        $this->assertEquals(1, count($c));
        $this->assertEquals('root-master_test1', $c[0]->componentId);
    }

    public function testEn()
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentsByClass('Vpc_Chained_Trl_Component.Vpc_Basic_Empty_Component');
        $this->assertEquals(1, count($c));
        $this->assertEquals('root-en_test1', $c[0]->componentId);
    }

    public function testGetComponentClassesByParentClass()
    {
        $c = 'Vpc_Chained_Trl_Component.Vpc_Basic_Empty_Component';
        $cls = Vpc_Abstract::getComponentClassesByParentClass($c);
        $this->assertEquals(array($c), $cls);

        $c = 'Vpc_Chained_Trl_Component.Vpc_Basic_Empty_Component';
        $cls = Vpc_Abstract::getComponentClassesByParentClass('Vpc_Chained_Trl_Component');
        $this->assertEquals(array($c), $cls);
    }
}
