<?php
/**
 * @group slow
 * @group selenium
 * @group Kwf_Js
 *
 * http://kwf.benjamin.vivid/kwf/test/kwf_js_on-ready-priority_test
 */
class Kwf_Js_OnReadyPriority_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function testJsEvent()
    {
        $this->open('/kwf/test/kwf_js_on-ready-priority_test');
        $this->assertEquals('7 6 5 4 3 2 1', $this->getText('css=#result'));
    }
}
