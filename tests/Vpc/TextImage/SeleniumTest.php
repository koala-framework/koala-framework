<?php
/**
 * @group slow
 * @group Vpc_TextImage
 */
class Vpc_TextImage_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Component_Data_Root::setComponentClass('Vpc_TextImage_Root');
    }

    public function testLightbox()
    {
        $this->openVpc('/textimage1');
        sleep(5);
        $this->assertContainsText('css=.vpcTextImageTestComponent .vpcText', 'foo');
        sleep(5);
        $this->assertElementNotPresent('css=.vpsLightbox');
        $this->assertElementPresent('css=.vpcTextImageTestComponent a img');
        $this->click('css=.vpcTextImageTestComponent a img');
        $this->waitForConnections();
        $this->assertVisible('css=.vpsLightbox');
        $this->assertElementPresent('css=.vpsLightbox div.image img');
        $this->click('css=.vpsLightbox a.closeButton');
        sleep(1);
        $this->assertNotVisible('css=.vpsLightbox');
    }

    public function testAdmin()
    {
        $this->openVpcEdit('Vpc_TextImage_TestComponent', 'root_textImage1');
        sleep(5);
        $this->waitForConnections();
        //test könnte natürlich verbessert werden, aber zumindest testen ob kein fehler kommt
    }
}
