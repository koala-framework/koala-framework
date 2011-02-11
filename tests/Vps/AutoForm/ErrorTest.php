<?php
/**
 * @group selenium
 * @group slow
 * @group AutoForm
 */
class Vps_AutoForm_ErrorTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTimeout(300000);
    }

    public function testAutoFormAddDisplayErrorsFalse()
    {
        $this->_testAutoForm(false);
    }

    public function testAutoFormAddDisplayErrorsTrue()
    {
        $this->_testAutoForm(true);
    }

    private function _testAutoForm($errors) {

        $this->open('/vps/test/vps_auto-form_test/reset');

        $this->open('/vps/test/vps_auto-form_test/get-row-count');
        $count = $this->getText('//body');
        $this->assertEquals(1, $count);

        $this->open('/vps/test/vps_auto-form_test');
        $this->waitForConnections();
        $this->type("//input[@name='foo']", "newValue");

        if (!$errors) $this->runScript('function foo() {Vps.Debug.displayErrors = false; Vps.log("selenium: "+Vps.Debug.displayErrors); }; foo();');
        else {
            $this->runScript('function foo() {Vps.Debug.displayErrors = true; Vps.log("selenium: "+Vps.Debug.displayErrors); }; foo();');
        }
        $this->click("//button[text()='".trlVps('Save')."']");
        $this->waitForConnections();

        if (!$errors) $button = trlVps('OK');
        else $button = trlVps('Retry');

        $this->click("//button[text()='".$button."']");
        $this->waitForConnections();
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
