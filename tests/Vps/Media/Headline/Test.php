<?php
/**
 * @group selenium
 * @group slow
 * @group Headline
 */
class Vps_Media_Headline_Test extends Vps_Test_SeleniumTestCase
{
    public function testIt()
    {
        $this->open('/vps/test/vps_media_headline_test');
        sleep(1);
        $this->assertElementPresent("css=h1.testHeadline img");
        $this->assertEquals($this->getText("css=h1.testHeadline"), "");
    }
}
