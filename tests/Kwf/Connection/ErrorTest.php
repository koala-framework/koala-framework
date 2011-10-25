<?php
/**
 * @group selenium
 * @group slow
 * @group Connection_Error
 */
class Kwf_Connection_ErrorTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTimeout(300000);
    }

    protected function defaultAssertions($command)
    {
    }

    public function testConnectionErrorDisplayErrorsFalse()
    {
        $this->_testError(false);
    }

    public function testConnectionErrorDisplayErrorsTrue()
    {
        $this->_testError(true);
    }

    private function _testError($errors)
    {
        $this->open('/kwf/test/kwf_connection_test');
        $this->waitForConnections();
        sleep(2);
        $this->click("//button[text()='testA']");
        $this->waitForConnections();
        if ($errors) $this->runScript('function foo() {Kwf.Debug.displayErrors = false; Kwf.log("selenium: "+Kwf.Debug.displayErrors); }; foo();');
        $this->click("//button[text()='".trlKwf('Retry')."']");
        $this->waitForConnections();
        $this->click("//button[text()='".trlKwf('Abort')."']");
        $this->assertEquals("abort", $this->getText('id=abort'));
    }

    public function testConnectionMoreRequests()
    {

        $this->open('/kwf/test/kwf_connection_test');
        $this->waitForConnections();
        sleep(2);
        $this->click("//button[text()='testC']");
        $this->waitForConnections();
        $this->click("//div[contains(text(),'timeoutError')]/../../../..//button[text()='".trlKwf("Retry")."']");
        $this->waitForConnections();
        $this->click("//div[contains(text(),'timeoutError')]/../../../..//button[text()='".trlKwf("Retry")."']");
        $this->waitForConnections();
        $this->click("//div[contains(text(),'timeoutError')]/../../../..//button[text()='".trlKwf("Retry")."']");
        $this->waitForConnections();

        $this->click("//*[contains(text(),'exceptionError')]/../../../../..//button[text()='".trlKwf("Retry")."']");
        $this->waitForConnections();
        $this->click("//*[contains(text(),'exceptionError')]/../../../../..//button[text()='".trlKwf("Retry")."']");
        $this->waitForConnections();
        $this->click("//*[contains(text(),'exceptionError')]/../../../../..//button[text()='".trlKwf("Retry")."']");
        $this->waitForConnections();
        $this->click("//*[contains(text(),'exceptionError')]/../../../../..//button[text()='".trlKwf("Retry")."']");
        $this->waitForConnections();

        $this->click("//*[contains(text(),'exceptionError')]/../../../../..//button[text()='".trlKwf("Abort")."']");
        $this->waitForConnections();

        $this->click("//div[contains(text(),'timeoutError')]/../../../..//button[text()='".trlKwf("Abort")."']");
        $this->waitForConnections();

        $this->open('/kwf/test/kwf_connection_test/get-timeouts');
        $count = $this->getText('//body');
        $this->assertEquals(4, $count);

        $this->open('/kwf/test/kwf_connection_test/get-exceptions');
        $count = $this->getText('//body');
        $this->assertEquals(5, $count);

    }

    public function testConnectionRealException()
    {
        $this->open('/kwf/test/kwf_connection_test');
        $this->waitForConnections();
        sleep(2);
        $this->click("//button[text()='testD']");
        $this->waitForConnections();
        $text = $this->getText("//*[contains(text(), 'Kwf_Exception')]");
        $this->assertTrue((bool)strpos($text, 'Connection/TestController.php:50'));
    }
}
