<?php
/**
 * @group slow
 * @group selenium
 */
class Kwf_Test_SeleniumTestCase_CookieTest extends Kwf_Test_SeleniumTestCase
{
    public function testCookie()
    {
        $this->open('/kwf/test/kwf_test_selenium-test-case_test/index');
        $this->assertEquals($this->_unitTestCookie, $this->getCookieByName('unitTest'));
    }
}
