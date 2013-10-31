<?php
class Kwf_Assets_Ext4_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function testIt()
    {
        $this->open('/kwf/test/kwf_assets_ext4_test');
        $this->assertBodyTextContains('windowtitle');
        $this->assertBodyTextContains('windowcontent');
    }
}
