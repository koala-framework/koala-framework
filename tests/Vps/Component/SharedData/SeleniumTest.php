<?php
/**
 * @group Component_SharedData
 * @group slow
 */
// /vps/componentedittest/Vps_Component_SharedData_Component/Vps_Component_SharedData_Detail_SharedData_Component?componentId=root
class Vps_Component_SharedData_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_SharedData_Component');
        parent::setUp();
    }

    // Vps_Component_SharedData_Detail_SharedData_Component wird bearbeitet, hat aber id von
    // Vps_Component_SharedData_Component -> muss also foo anzeigen
    public function testIt()
    {
        $this->openVpcEdit('Vps_Component_SharedData_Detail_SharedData_Component', 'root');
        $this->waitForConnections();
        $this->assertElementValueEquals("css=input.x-form-text", 'foo');
    }
}
