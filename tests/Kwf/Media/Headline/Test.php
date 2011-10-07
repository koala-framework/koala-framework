<?php
/**
 * @group selenium
 * @group slow
 * @group Headline
 */
class Kwf_Media_Headline_Test extends Kwf_Test_SeleniumTestCase
{
    public function testIt()
    {
        $this->open('/kwf/test/kwf_media_headline_test');
        sleep(1);
        $this->assertElementPresent("css=h1.testHeadline img");
        $this->assertEquals($this->getText("css=h1.testHeadline"), "");
    }
}
