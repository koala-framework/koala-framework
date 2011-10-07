<?php
/**
 * @group slow
 * @group selenium
 * @group Composite_ImagesEnlarge
 */
class Vpc_Composite_ImagesEnlarge_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Component_Data_Root::setComponentClass('Vpc_Composite_ImagesEnlarge_Root');
    }

    public function testNextPrevLink()
    {
        $this->openVpc('/foo');
        $this->assertElementPresent('css=.vpcCompositeImagesEnlarge a img');
        $this->click('css=a');
        $this->waitForConnections();
        $this->assertElementPresent('css=.vpsLightboxOpen');
        $this->assertVisible('css=.vpsLightboxOpen');
        $this->assertElementPresent('css=.vpsLightboxOpen div.image');
        $this->assertElementPresent('css=.vpsLightboxOpen div.image img');
        $this->assertElementPresent('css=.vpsLightboxOpen .nextBtn a');
        $this->assertElementNotPresent('css=.vpsLightboxOpen .prevBtn a');
        $this->click('css=.vpsLightboxOpen .nextBtn a');
        $this->assertElementPresent('css=.vpsLightboxOpen .nextBtn a');
        $this->assertElementPresent('css=.vpsLightboxOpen .prevBtn a');
        $this->click('css=.vpsLightboxOpen .nextBtn a');
        $this->assertElementNotPresent('css=.vpsLightboxOpen .nextBtn a');
        $this->assertElementPresent('css=.vpsLightboxOpen .prevBtn a');
        $this->click('css=.vpsLightboxOpen .prevBtn a');
        $this->assertElementPresent('css=.vpsLightboxOpen .nextBtn a');
        $this->assertElementPresent('css=.vpsLightboxOpen .prevBtn a');
    }
}
