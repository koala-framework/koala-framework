<?php
/**
 * @group slow
 */
class Vps_Test_SeleniumTestCase_CookieTest extends Vps_Test_SeleniumTestCase
{
    public function testCookie()
    {
        $this->open('/');
        $this->assertEquals($this->_unitTestCookie, $this->getCookieByName('unitTest'));
    }
}
