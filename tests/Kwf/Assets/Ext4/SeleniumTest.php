<?php
/**
 * @group selenium
 * @group slow
 */
class Kwf_Assets_Ext4_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function testIt()
    {
        $this->open('/kwf/test/kwf_assets_ext4_test');
        $this->assertBodyTextContains('windowtitle');
        $this->assertBodyTextContains('windowcontent');
    }

    public function testLazyLoad()
    {
        $this->open('/kwf/test/kwf_assets_ext4_test/lazy-load');
        sleep(3);
        $this->waitForCondition('!selenium.browserbot.getCurrentWindow().window.winLoaded');
        $this->assertBodyTextContains('windowtitle');
        $this->assertBodyTextContains('windowcontent');
    }
}
