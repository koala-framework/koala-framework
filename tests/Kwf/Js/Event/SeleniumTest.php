<?php
/**
 * @group slow
 * @group selenium
 * @group Kwf_Js
 *
 * http://kwf.markus.vivid/kwf/test/kwf_js_event_test
 */
class Kwf_Js_Event_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function testJsEvent()
    {
        $this->open('/kwf/test/kwf_js_event_test');

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
