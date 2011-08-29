<?php
/**
 * @group slow
 * @group selenium
 * @group Vps_Js
 * @group Vps_Js_EyeCandy_List
 *
 * http://vps.markus.vivid/vps/test/vps_js_event_test
 */
class Vps_EyeCandy_List_Plugins_Hover_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function testEyeCandyList()
    {
        $this->open('/vps/test/vps_eye-candy_list_plugins_hover_test');
        $checkStr = '';

        $this->mouseOver('css=#ti1');
        $checkStr .= 'childStateChanged|idx:0|state:large---';
        $this->assertEquals($this->getText('css=#result'), $checkStr);

        $this->mouseOut('css=#ti1');
        $checkStr .= 'childStateChanged|idx:0|state:normal---';
        $this->assertEquals($this->getText('css=#result'), $checkStr);
    }
}
