<?php
/**
 * @group ChildSettings
 */
class Kwf_Component_ChildSettings_Composite_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_ChildSettings_Composite_Root');
    }

    public function testIt()
    {
        $gen = Kwc_Abstract::getSetting('Kwf_Component_ChildSettings_Composite_Root', 'generators');

        $c = $gen['child']['component']['child1'];
        $this->assertTrue(is_instance_of($c, 'Kwc_Basic_Empty_Component'));

        $this->assertEquals(Kwc_Abstract::getSetting($c, 'componentName'), 'child1name');


        $c = $gen['child']['component']['child2'];
        $this->assertTrue(is_instance_of($c, 'Kwc_Basic_Empty_Component'));

        $this->assertEquals(Kwc_Abstract::getSetting($c, 'componentName'), 'child2name');
    }
}
