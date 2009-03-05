<?php
/**
 * @group selenium
 * @group slow
 * @group AutoForm
 * @group Form_GoogleMap
 */
class Vps_Form_GoogleMap_ErrorTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTimeout(120000);
    }

    public function testGoogleNewOpenWindow()
    {
        $this->open('/vps/test/vps_form_google-map_test');
        $this->waitForConnections();
        $this->click("//input[@name='mapEmpty']/../..//img");
        $this->assertNotNull($this->getText("//span[text()='Adresse eingeben.']"));
    }

    public function testGoogleOldOpenWindow()
    {
        $this->open('/vps/test/vps_form_google-map_test');
        $this->waitForConnections();
        $this->click("//input[@name='mapSelected']/../..//img");
        $this->assertElementNotPresent("//span[text()='Adresse eingeben.']");
    }

}
