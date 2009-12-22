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
        $this->assertNotVisible('css=.lightbox');
        $this->assertElementPresent('css=.vpcTextImageTestComponent a img');
        $this->click('css=.vpcTextImageTestComponent a img');
        $this->assertVisible('css=.lightbox');
        $this->assertElementPresent('css=.lightbox img.centerImage');
        $this->click('css=.lightbox a.closeButton');
        $this->assertNotVisible('css=.lightbox');
    }

    public function testAdmin()
    {
        $this->openVpcEdit('Vpc_TextImage_TestComponent', 'root_textImage1');
        $this->waitForConnections();
        //test könnte natürlich verbessert werden, aber zumindest testen ob kein fehler kommt
    }
}
