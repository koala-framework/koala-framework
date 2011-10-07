<?php
/**
 * @group slow
 * @group selenium
 */
class Vps_Test_SeleniumTestCase_CookieTest extends Vps_Test_SeleniumTestCase
{
    public function testCookie()
    {
        $this->open('/vps/test/vps_test_selenium-test-case_test/index');
        $this->assertEquals($this->_unitTestCookie, $this->getCookieByName('unitTest'));
    }
}
