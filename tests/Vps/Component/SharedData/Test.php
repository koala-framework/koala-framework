<?php
/**
 * @group Component_SharedData
 */
class Vps_Component_SharedData_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_SharedData_Component');
    }

    public function testIt()
    {
        $root = $this->_root;
        $component = $root->getComponentById('root_2-shared');

        $sharedComponents = Vps_Controller_Action_Component_PagesController::getSharedComponents($root);
        $expected = array($component->componentClass => $root);
        $this->assertEquals($expected, $sharedComponents);

        $row = $component->getComponent()->getRow();
        $this->assertEquals('root', $row->component_id);
        $this->assertEquals('foo', $row->text);
    }
}
