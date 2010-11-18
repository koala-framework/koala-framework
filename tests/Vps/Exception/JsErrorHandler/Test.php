<?php
/**
 * @group selenium
 * @group slow
 * @group JsErrorHandler
 */
class Vps_Exception_JsErrorHandler_Test extends Vps_Test_SeleniumTestCase
{
    public function testIt()
    {
        $this->open('/vps/test/vps_exception_js-error-handler_test');
        $this->_testError(1, "test is null");
        $this->_testError(2, "Hello");
        $this->_testError(3, "Hello again");
        $this->_testError(4, "bject");
        $this->_testError(5, "test2 is null");
        $this->_testError(6, "Goodbye");
        $this->_testError(7, "Goodbye again");
        $this->_testError(8, "stuff");
    }

    private function _testError($num, $expectedMessage)
    {
        $start = file_get_contents('http://'.Vps_Registry::get('testDomain').'/vps/test/vps_exception_js-error-handler_test/get-time');
        $this->click('link=testError'.$num);
        sleep(1);
        $error = file_get_contents('http://'.Vps_Registry::get('testDomain').'/vps/test/vps_exception_js-error-handler_test/get-error-log-entry?start='.$start);
        if (!$error) {
            $this->fail('error file not found');
        }
        $this->assertContains($expectedMessage, $error);
    }
}
