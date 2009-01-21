<?php
/**
 * @group selenium
 * @group slow
 */
class Vps_Connection_ErrorTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTimeout(120000);
    }
    public function testConnectionErrorDisplayErrorsFalse()
    {
        $this->_testError(false);
    }

    public function testConnectionErrorDisplayErrorsTrue()
    {
        $this->_testError(true);
    }

    private function _testError($errors) {
        $this->open('/vps/test/vps_connection_test');
        $this->click("//button[text()='testA']");
        $this->waitForConnections();
        if ($errors) $this->runScript('function foo() {Vps.Debug.displayErrors = false; Vps.log("selenium: "+Vps.Debug.displayErrors); }; foo();');
        $this->click("//button[text()='".trlVps('Retry')."']");
        $this->waitForConnections();
        $this->click("//button[text()='".trlVps('Abort')."']");
        $this->assertEquals("abort", $this->getText('id=abort'));


    }

    public function testConnectionSuccess()
    {
        $this->open('/vps/test/vps_connection_test');
        $this->click("//button[text()='testB']");
        $this->waitForConnections();
        $this->assertEquals("success", $this->getText('id=success'));
    }

    public function testConnectionMoreRequests()
    {

        $this->open('/vps/test/vps_connection_test');
        $this->click("//button[text()='testC']");
        $this->waitForConnections();
        $this->click("//div[contains(text(),'timeoutError')]/../../../..//button[text()='".trlVps("Retry")."']");
        $this->waitForConnections();
        $this->click("//div[contains(text(),'timeoutError')]/../../../..//button[text()='".trlVps("Retry")."']");
        $this->waitForConnections();
        $this->click("//div[contains(text(),'timeoutError')]/../../../..//button[text()='".trlVps("Retry")."']");
        $this->waitForConnections();

        $this->click("//div[contains(text(),'exceptionError')]/../../../..//button[text()='".trlVps("Retry")."']");
        $this->waitForConnections();
        $this->click("//div[contains(text(),'exceptionError')]/../../../..//button[text()='".trlVps("Retry")."']");
        $this->waitForConnections();
        $this->click("//div[contains(text(),'exceptionError')]/../../../..//button[text()='".trlVps("Retry")."']");
        $this->waitForConnections();
        $this->click("//div[contains(text(),'exceptionError')]/../../../..//button[text()='".trlVps("Retry")."']");
        $this->waitForConnections();

        $this->click("//div[contains(text(),'exceptionError')]/../../../..//button[text()='".trlVps("Abort")."']");
        $this->waitForConnections();

        $this->click("//div[contains(text(),'timeoutError')]/../../../..//button[text()='".trlVps("Abort")."']");
        $this->waitForConnections();

        $this->open('/vps/test/vps_connection_test/get-timeouts');
        $count = $this->getText('//body');
        $this->assertEquals(4, $count);

        $this->open('/vps/test/vps_connection_test/get-exceptions');
        $count = $this->getText('//body');
        $this->assertEquals(5, $count);

    }

    public function testConnectionRealException()
    {
        $this->open('/vps/test/vps_connection_test');
        $this->click("//button[text()='testD']");
        $this->waitForConnections();
        $text = $this->getText("//div[contains(text(), 'Vps_Exception')]");
        $this->assertTrue((bool)strpos($text, 'Connection/TestController.php:55'));
    }
}
