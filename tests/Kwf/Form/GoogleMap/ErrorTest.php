<?php
/**
 * @group selenium
 * @group slow
 * @group AutoForm
 * @group Form_GoogleMap
 */
class Kwf_Form_GoogleMap_ErrorTest extends Kwf_Test_SeleniumTestCase
{
    public function testGoogleNewOpenWindow()
    {
        $this->open('/kwf/test/kwf_form_google-map_test');
        $this->waitForConnections();
        $this->click("//input[@name='mapEmpty']/../..//img");
        $this->assertNotNull($this->getText("//span[text()='".trlKwf('enter address')."']"));

        $this->open('/kwf/test/kwf_form_google-map_test');
        $this->waitForConnections();
        $this->click("//input[@name='mapSelected']/../..//img");
        $this->assertElementNotPresent("//span[text()='".trlKwf('enter address')."']");
    }
}
