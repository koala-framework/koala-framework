<?php
/**
 * @group selenium
 * @group slow
 * @group AutoForm
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
        $this->click("//input[@name='mapEmpty']/../..//img");
        $this->assertNotNull($this->getText("//span[text()='Adresse eingeben.']"));
    }

    public function testGoogleOldOpenWindow()
    {
        $this->open('/vps/test/vps_form_google-map_test');
        $this->click("//input[@name='mapSelected']/../..//img");
        try {
            //hier muss eine selenium exception geworfen werden
            $this->getText("//span[text()='Adresse eingeben.']");
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        sleep(5);
    }

}
