<?php
/**
 * @group Component_SharedData
 * @group slow
 * @group selenium
 */
// /kwf/componentedittest/Kwf_Component_SharedData_Component/Kwf_Component_SharedData_Detail_SharedData_Component?componentId=root
class Kwf_Component_SharedData_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        Kwf_Component_Data_Root::setComponentClass('Kwf_Component_SharedData_Component');
        parent::setUp();
    }

    // Kwf_Component_SharedData_Detail_SharedData_Component wird bearbeitet, hat aber id von
    // Kwf_Component_SharedData_Component -> muss also foo anzeigen
    public function testIt()
    {
        $this->openKwcEdit('Kwf_Component_SharedData_Detail_SharedData_Component', 'root');
        $this->waitForConnections();
        $this->assertElementValueEquals("css=input.x2-form-text", 'foo');
    }
}
