<?php
/**
 * @group selenium
 * @group slow
 */
class Kwc_Advanced_SocialBookmarks_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        Kwf_Component_Data_Root::setComponentClass('Kwc_Advanced_SocialBookmarks_Root');
        parent::setUp();
    }
    public function testIt()
    {
        $this->openKwc('/page1');
        $this->assertElementPresent('css=a');
        $this->openKwc('/page1/page2');
        $this->assertElementPresent('css=a');
    }
}
