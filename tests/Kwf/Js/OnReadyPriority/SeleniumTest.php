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
        // Checking priority set for elementReady and contentReady
        $this->open('/kwf/test/kwf_js_on-ready-priority_test');
        $this->assertEquals('7 6 5 4 3 2 1', $this->getText('css=#result'));

        // Checking if dynamically added div is detected by elementReady and
        // hidden elements are ignored
        $this->click("add");
        $this->assertEquals('7 6 3 2 1', $this->getText('css=#result'));

        // Checking if change of visibility does fire elementReady
        $this->click("changeVisibility");
        $this->assertEquals('7 6 2 1', $this->getText('css=#result'));
    }
}
