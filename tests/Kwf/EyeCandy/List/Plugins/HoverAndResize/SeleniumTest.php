<?php
/**
 * @group slow
 * @group selenium
 * @group Kwf_Js
 * @group Kwf_Js_EyeCandy_List
 * @group Kwf_Js_EyeCandy_List_Plugins_HoverAndResize
 *
 */
class Kwf_EyeCandy_List_Plugins_HoverAndResize_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function testEyeCandyList()
    {
        $this->open('/kwf/test/kwf_eye-candy_list_plugins_hover-and-resize_test');
        $this->assertEquals($this->getElementWidth('css=#ti1'), 100); //initial size

        $this->mouseOver('css=#ti1');
        sleep(1); //wait for animation finished
        $this->assertEquals($this->getElementWidth('css=#ti1'), 200);

        $this->mouseOut('css=#ti1');
        sleep(1); //wait for animation finished
        $this->assertEquals($this->getElementWidth('css=#ti1'), 100);
    }
}
