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
        $start = time();
        $this->click('link=testError'.$num);
        sleep(1);
        $this->assertTextPresent(trlVps('An error has occured'));
        $this->click("//button[text()='OK']");
        $error = false;
        clearstatcache();
        foreach (glob('application/log/error/'.date('Y-m-d').'/*.txt') as $f) {
            preg_match('#([0-9]{2})_([0-9]{2})_([0-9]{2})_#', $f, $m);
            $t = strtotime(date('Y-m-d').' '.$m[1].':'.$m[2].':'.$m[3]);
            if ($t >= $start) {
                $error = file_get_contents($f);
                if (strpos($error, 'Vps_Exception_JavaScript')!==false) {
                    unlink($f);
                }
            }
        }
        if (!$error) {
            $this->fail('error file not found');
        }
        $this->assertContains($expectedMessage, $error);
    }
}
