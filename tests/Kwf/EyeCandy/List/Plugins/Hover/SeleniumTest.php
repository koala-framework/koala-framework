<?php
/**
 * @group slow
 * @group selenium
 * @group Kwf_Js
 * @group Kwf_Js_EyeCandy_List
 *
 */
class Kwf_EyeCandy_List_Plugins_Hover_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function testEyeCandyList()
    {
        $this->open('/kwf/test/kwf_eye-candy_list_plugins_hover_test');
        $checkStr = '';

        $this->mouseOver('css=#ti1');
        $checkStr .= 'childStateChanged|idx:0|state:large---';
        $this->assertEquals($this->getText('css=#result'), $checkStr);

        $this->mouseOut('css=#ti1');
        $checkStr .= 'childStateChanged|idx:0|state:normal---';
        $this->assertEquals($this->getText('css=#result'), $checkStr);
    }
}
