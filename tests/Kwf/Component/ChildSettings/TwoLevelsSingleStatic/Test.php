<?php
/**
 * @group ChildSettings
 */
class Kwf_Component_ChildSettings_TwoLevelsSingleStatic_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_ChildSettings_TwoLevelsSingleStatic_Root');
    }

    public function testIt()
    {
        $gen = Kwc_Abstract::getSetting('Kwf_Component_ChildSettings_TwoLevelsSingleStatic_Root', 'generators');
        $c = $gen['first']['component'];
        $this->assertTrue(is_instance_of($c, 'Kwf_Component_ChildSettings_TwoLevelsSingleStatic_First'));

        $gen = Kwc_Abstract::getSetting($c, 'generators');
        $c = $gen['second']['component'];
        $this->assertTrue(is_instance_of($c, 'Kwc_Basic_Empty_Component'));

        $this->assertEquals(Kwc_Abstract::getSetting($c, 'componentName'), 'test123');

    }
}