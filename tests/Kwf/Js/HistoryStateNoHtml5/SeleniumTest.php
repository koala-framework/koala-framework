<?php
/**
 * @group slow
 * @group selenium
 * @group Kwf_Js
 *
 * http://kwf.niko.vivid/kwf/test/kwf_js_history-state-no-html5_test
 */
class Kwf_Js_HistoryStateNoHtml5_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function testJsEvent()
    {
        $this->open('/kwf/test/kwf_js_history-state-no-html5_test');

        $this->assertEquals('index', $this->getText('css=#result'));

        $this->click('css=#testBtn1');
        $this->assertEquals('sub', $this->getText('css=#result'));

        sleep(1);
        $this->getEval('selenium.browserbot.getCurrentWindow().history.back();');
        sleep(1);
        $this->assertEquals('index', $this->getText('css=#result'));
        sleep(1);
        $this->getEval('selenium.browserbot.getCurrentWindow().history.forward();');
        sleep(1);
        $this->assertEquals("sub", $this->getText('css=#result'));

        $this->refreshAndWait();
        $this->assertEquals('sub', $this->getText('css=#result'));
    }
}
