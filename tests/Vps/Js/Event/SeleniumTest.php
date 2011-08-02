<?php
/**
 * @group slow
 * @group selenium
 * @group Vps_Js
 *
 * http://vps.markus.vivid/vps/test/vps_js_event_test
 */
class Vps_Js_Event_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function testJsEvent()
    {
        $this->open('/vps/test/vps_js_event_test');

        $this->mouseOver('css=#eventTest');
        $this->assertEquals($this->getText('css=#result'), 'mouseEnter: enter ---');
        
        $this->mouseOver('css=#eventTestA');
        $this->mouseOver('css=#eventTestStrong');
        $this->mouseOver('css=#eventTestSpan');
        $this->mouseOut('css=#eventTestSpan');
        $this->assertEquals('mouseEnter: enter ---mouseLeave: leave ---', $this->getText('css=#result'));

        $this->mouseOver('css=#eventTestSpan');
        $this->assertEquals('mouseEnter: enter ---mouseLeave: leave ---mouseEnter: enter ---', $this->getText('css=#result'));
        $this->mouseOut('css=#eventTestSpan');
        $this->assertEquals('mouseEnter: enter ---mouseLeave: leave ---mouseEnter: enter ---mouseLeave: leave ---', $this->getText('css=#result'));
    }
}
