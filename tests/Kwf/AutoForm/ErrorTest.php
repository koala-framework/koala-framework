<?php
/**
 * @group selenium
 * @group slow
 * @group AutoForm
 */
class Kwf_AutoForm_ErrorTest extends Kwf_Test_SeleniumTestCase
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

        $this->open('/kwf/test/kwf_auto-form_test/reset');

        $this->open('/kwf/test/kwf_auto-form_test/get-row-count');
        $count = $this->getText('//body');
        $this->assertEquals(1, $count);

        $this->open('/kwf/test/kwf_auto-form_test');
        $this->waitForConnections();
        $this->type("//input[@name='foo']", "newValue");

        if (!$errors) $this->runScript('function foo() {Kwf.Debug.displayErrors = false; Kwf.log("selenium: "+Kwf.Debug.displayErrors); }; foo();');
        else {
            $this->runScript('function foo() {Kwf.Debug.displayErrors = true; Kwf.log("selenium: "+Kwf.Debug.displayErrors); }; foo();');
        }
        $this->click("//button[text()='".trlKwf('Save')."']");
        $this->waitForConnections();

        if (!$errors) $button = trlKwf('OK');
        else $button = trlKwf('Retry');

        $this->click("//button[text()='".$button."']");
        $this->waitForConnections();
        sleep(1);

        $this->open('/kwf/test/kwf_auto-form_test/get-row-count');
        $count = $this->getText('//body');
        $this->assertEquals(2, $count);

    }

    protected function defaultAssertions($action)
    {
        //do nothing .. hier werden fehler aufgerufen
    }
}
