<?php
/**
 * @group slow
 * @group selenium
 * @group Kwf_Js
 * @group Kwf_Js_EyeCandy_List
 *
 * http://kwf.markus.vivid/kwf/test/kwf_js_event_test
 */
class Kwf_EyeCandy_List_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function testEyeCandyList()
    {
        $this->open('/kwf/test/kwf_eye-candy_list_test');
        $checkStr = '';

        $this->mouseOver('css=#ti2');
        $checkStr .= 'childMouseEnter|cnt:5|idx:1---';
        $this->assertEquals($this->getText('css=#result'), $checkStr);

        $this->mouseOut('css=#ti2');
        $checkStr .= 'childMouseLeave|cnt:5|idx:1---';
        $this->assertEquals($this->getText('css=#result'), $checkStr);

        $this->mouseOver('css=#nerv1');
        $this->mouseOut('css=#nerv1');
        $this->assertEquals($this->getText('css=#result'), $checkStr);

        $this->mouseOver('css=#ti5');
        $this->mouseOut('css=#ti5');
        $this->mouseOver('css=#ti5');
        $checkStr .= 'childMouseEnter|cnt:5|idx:4---childMouseLeave|cnt:5|idx:4---childMouseEnter|cnt:5|idx:4---';
        $this->assertEquals($this->getText('css=#result'), $checkStr);

        $this->click('css=#ti4');
        $checkStr .= 'childClick|cnt:5|idx:3---';
        $this->assertEquals($this->getText('css=#result'), $checkStr);
    }
}
