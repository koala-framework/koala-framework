<?php
/**
 * @group Component_SharedData
 */
class Vps_Component_SharedData_Test extends PHPUnit_Framework_TestCase
{
    public function testIt()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_SharedData_Component');
        $root = Vps_Component_Data_Root::getInstance();
        $component = $root->getComponentById('root_2-shared');

        $sharedComponents = Vps_Controller_Action_Component_PagesController::getSharedComponents($root);
        $expected = array($component->componentClass => $root);
        $this->assertEquals($expected, $sharedComponents);

        $row = $component->getComponent()->getRow();
        $this->assertEquals('root', $row->component_id);
        $this->assertEquals('foo', $row->text);
    }
}
