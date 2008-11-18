<?php
/**
 * @group selenium
 */
class Vps_Connection_ErrorTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTimeout(120000);
    }
    public function testErrorDisplayErrorsFalse()
    {
        $this->_testError(false);
    }

    public function testErrorDisplayErrorsTrue()
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

    public function testSuccess()
    {
        $this->open('/vps/test/vps_connection_test');
        $this->click("//button[text()='testB']");
        $this->waitForConnections();
        $this->assertEquals("success", $this->getText('id=success'));
    }

    public function testMoreRequests()
    {
        $this->markTestIncomplete();

        $this->open('/vps/test/vps_connection_test');
        $this->click("//button[text()='testC']");
        $this->waitForConnections();
        // $this->click("//button[text()='Retry']");
        /*$this->click("dom=function foo() {
        var ret;
        Ext.ComponentMgr.all.each(function(c) {
            if (c instanceof Ext.Window && c.title == 'exceptionError') {
                c.buttons.each(function (b) {
                    if (b.text == 'Retry') {
                        ret = b.el.dom;
                    }
                });
            }
         });
         return ret;
         }
        foo();");
       // $this->getText(//)
        //$this->click("////button[text()='Abort']");
    }
}
