<?php
/**
 * @group slow
 * @group selenium
 * @group Kwf_Js
 *
 * http://kwf.niko.vivid/kwf/test/kwf_js_clear-on-focus_test
 */
class Kwf_Js_ClearOnFocus_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function testInput()
    {
        $this->open('/kwf/test/kwf_js_clear-on-focus_test');

        $this->assertEquals($this->getValue('css=.test1'), 'foo');
        $this->focus('css=.test1');
        $this->assertEquals($this->getValue('css=.test1'), '');
        $this->focus('css=.test2');
        $this->assertEquals($this->getValue('css=.test1'), 'foo');
        $this->assertEquals($this->getValue('css=.test2'), 'foo');
    }

    public function testTextArea()
    {
        $this->open('/kwf/test/kwf_js_clear-on-focus_test');

        $this->assertEquals($this->getValue('css=.test3'), 'foo');
        $this->focus('css=.test3');
        $this->assertEquals($this->getValue('css=.test3'), '');
        $this->focus('css=.test4');
        $this->assertEquals($this->getValue('css=.test3'), 'foo');
        $this->assertEquals($this->getValue('css=.test4'), 'foo');
    }
}
