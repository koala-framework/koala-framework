<?php
/**
 * @group ChildSettings
 */
class Kwf_Component_ChildSettings_SingleStatic_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_ChildSettings_SingleStatic_Root');
    }

    public function testIt()
    {
        $gen = Kwc_Abstract::getSetting('Kwf_Component_ChildSettings_SingleStatic_Root', 'generators');
        $c = $gen['empty']['component'];
        $this->assertTrue(is_instance_of($c, 'Kwc_Basic_Empty_Component'));

        $this->assertEquals(Kwc_Abstract::getSetting($c, 'componentName'), 'test123');
    }
}
