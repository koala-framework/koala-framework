<?php
/**
 * @group selenium
 * @group slow
 */
class Vpc_Advanced_SocialBookmarks_Test extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Advanced_SocialBookmarks_Root');
        parent::setUp();
    }
    public function testIt()
    {
        $this->openVpc('/page1');
        $this->assertElementPresent('css=a');
        $this->openVpc('/page1/page2');
        $this->assertElementPresent('css=a');
    }
}
