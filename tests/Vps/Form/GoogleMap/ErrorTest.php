<?php
/**
 * @group selenium
 * @group slow
 * @group AutoForm
 * @group Form_GoogleMap
 */
class Vps_Form_GoogleMap_ErrorTest extends Vps_Test_SeleniumTestCase
{
    public function testGoogleNewOpenWindow()
    {
        $this->open('/vps/test/vps_form_google-map_test');
        $this->waitForConnections();
        $this->click("//input[@name='mapEmpty']/../..//img");
        $this->assertNotNull($this->getText("//span[text()='".trlVps('enter address')."']"));

        $this->open('/vps/test/vps_form_google-map_test');
        $this->waitForConnections();
        $this->click("//input[@name='mapSelected']/../..//img");
        $this->assertElementNotPresent("//span[text()='".trlVps('enter address')."']");
    }

}
