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
        $this->assertTrue(is_array($c));
        $this->assertTrue(is_instance_of($c['first'], 'Kwf_Component_ChildSettings_TwoLevelsSingleStatic_First'));

        $gen = Kwc_Abstract::getSetting($c['first'], 'generators');
        $c = $gen['second']['component'];
        $this->assertTrue(is_array($c));
        $this->assertTrue(is_instance_of($c['second'], 'Kwc_Basic_None_Component'));

        $this->assertEquals(Kwc_Abstract::getSetting($c['second'], 'componentName'), 'test123');

    }
}
