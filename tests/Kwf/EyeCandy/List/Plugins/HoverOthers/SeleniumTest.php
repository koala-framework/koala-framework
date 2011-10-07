<?php
/**
 * @group slow
 * @group selenium
 * @group Vps_Js
 * @group Vps_Js_EyeCandy_List_Plugins_HoverOthers
 *
 */
class Vps_EyeCandy_List_Plugins_HoverOthers_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function testEyeCandyList()
    {
        $this->open('/vps/test/vps_eye-candy_list_plugins_hover-others_test');
        $checkStr = '';

        $this->mouseOver('css=#ti1');
        $checkStr .= 'childStateChanged|idx:2|state:tiny---';
        $checkStr .= 'childStateChanged|idx:3|state:tiny---';
        $checkStr .= 'childStateChanged|idx:4|state:tiny---';
        $this->assertEquals($this->getText('css=#result'), $checkStr);

        $this->mouseOut('css=#ti1');
        $checkStr .= 'childStateChanged|idx:2|state:normal---';
        $checkStr .= 'childStateChanged|idx:3|state:normal---';
        $checkStr .= 'childStateChanged|idx:4|state:normal---';
        $this->assertEquals($this->getText('css=#result'), $checkStr);


        $this->mouseOver('css=#ti4');
        $checkStr .= 'childStateChanged|idx:1|state:tiny---';
        $checkStr .= 'childStateChanged|idx:0|state:tiny---';
        $this->assertEquals($this->getText('css=#result'), $checkStr);

        $this->mouseOut('css=#ti4');
        $checkStr .= 'childStateChanged|idx:1|state:normal---';
        $checkStr .= 'childStateChanged|idx:0|state:normal---';
        $this->assertEquals($this->getText('css=#result'), $checkStr);
    }
}
