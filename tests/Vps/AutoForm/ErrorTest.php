<?php
/**
 * @group selenium
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
        $model = Vps_Model_Abstract::getInstance('Vps_AutoForm_TestModel');
        $this->open('/vps/test/vps_auto-form_test');
        $this->waitForConnections();
        $this->type("//input[@name='foo']", "newValue");
        $this->click("//button[text()='".trlVps('Save')."']");
        $this->waitForConnections();
        $this->click("//button[text()='".trlVps('Retry')."']");

        $model->reloadSession();
        $rows = $model->countRows();
        $this->assertEquals(2, $rows);
    }

    protected function defaultAssertions($action)
    {
        //do nothing .. hier werden fehler aufgerufen
    }
}
