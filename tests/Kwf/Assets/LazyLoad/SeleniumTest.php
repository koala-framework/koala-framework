<?php
/**
 * @group selenium
 * @group slow
 */
class Kwf_Assets_LazyLoad_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function testIt()
    {
        $this->open('/kwf/test/kwf_assets_lazy-load_test');
        sleep(3);
        $this->waitForCondition('!selenium.browserbot.getCurrentWindow().window.Foo');
        $this->assertBodyTextContains('BarFoo');
    }
}
