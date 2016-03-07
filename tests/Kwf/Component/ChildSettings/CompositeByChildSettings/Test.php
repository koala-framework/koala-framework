<?php
/**
 * @group ChildSettings
 */
class Kwf_Component_ChildSettings_CompositeByChildSettings_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_ChildSettings_CompositeByChildSettings_Root');
    }

    public function testIt()
    {
        $gen = Kwc_Abstract::getSetting('Kwf_Component_ChildSettings_CompositeByChildSettings_Root', 'generators');
        $c = $gen['first']['component'];
        $this->assertTrue(is_array($c));
        $this->assertTrue(is_instance_of($c['first'], 'Kwc_Abstract_Composite_Component'));

        $gen = Kwc_Abstract::getSetting($c['first'], 'generators');
        $c = $gen['child']['component'];
        $this->assertTrue(is_instance_of($c['second1'], 'Kwc_Basic_None_Component'));
        $this->assertTrue(is_instance_of($c['second2'], 'Kwc_Basic_None_Component'));

        $this->assertEquals(Kwc_Abstract::getSetting($c['second1'], 'componentName'), 'second1name');
        $this->assertEquals(Kwc_Abstract::getSetting($c['second2'], 'componentName'), 'second2name');
    }
}
