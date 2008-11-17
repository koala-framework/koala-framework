<?php
/**
 * @group selenium
 * @group AutoForm
 */
class Vps_AutoForm_ErrorTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTimeout(120000);
    }

    public function testAutoFormAdd()
    {
        $this->open('/vps/test/vps_auto-form_test/get-row-count');
        $count = $this->getText('//body');
        $this->assertEquals(1, $count);

        $this->open('/vps/test/vps_auto-form_test');
        $this->waitForConnections();
        $this->type("//input[@name='foo']", "newValue");
        $this->click("//button[text()='".trlVps('Save')."']");
        $this->waitForConnections();
        $this->click("//button[text()='".trlVps('Retry')."']");
        sleep(1);


        $this->open('/vps/test/vps_auto-form_test/get-row-count');
        $count = $this->getText('//body');
        $this->assertEquals(2, $count);
    }

    protected function defaultAssertions($action)
    {
        //do nothing .. hier werden fehler aufgerufen
    }
}
