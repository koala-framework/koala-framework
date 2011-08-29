<?php
/**
 * @group slow
 * @group selenium
 * @group Vps_Js
 * @group Vps_Js_EyeCandy_List
 * @group Vps_Js_EyeCandy_List_Plugins_HoverAndResize
 *
 * http://vps.markus.vivid/vps/test/vps_js_event_test
 */
class Vps_EyeCandy_List_Plugins_HoverAndResize_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function testEyeCandyList()
    {
        $this->open('/vps/test/vps_eye-candy_list_plugins_hover-and-resize_test');
        $this->assertEquals($this->getElementWidth('css=#ti1'), 100); //initial size

        $this->mouseOver('css=#ti1');
        sleep(1); //wait for animation finished
        $this->assertEquals($this->getElementWidth('css=#ti1'), 200);

        $this->mouseOut('css=#ti1');
        sleep(1); //wait for animation finished
        $this->assertEquals($this->getElementWidth('css=#ti1'), 100);
    }
}
