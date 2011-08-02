<?php
/**
 * @group slow
 * @group selenium
 * @group Vpc_Js
 *
 * http://vps.markus.vivid/vps/vpctest/Vpc_Js_Event_Root/js
 */
class Vpc_Js_Event_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Component_Data_Root::setComponentClass('Vpc_Js_Event_Root');
    }

    public function testForm()
    {
        $this->openVpc('/js');

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
