<?php
/**
 * @group slow
 * @group selenium
 * @group Basic_ImageEnlarge
 * @group Image
 */
class Vpc_Basic_ImageEnlarge_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Component_Data_Root::setComponentClass('Vpc_Basic_ImageEnlarge_Root');
    }

    public function testLightbox()
    {
        $this->openVpc('/foo1');
        $this->assertElementNotPresent('css=.vpsLightbox');
        $this->assertElementPresent('css=.vpcBasicImageEnlargeWithoutSmallImageComponent a img');
        $this->click('//a');
        $this->waitForConnections();
        $this->assertVisible('css=.vpsLightbox');
        $this->assertElementPresent('css=.vpsLightbox div.image');
        $this->assertElementPresent('css=.vpsLightbox div.image img');
        $this->click('css=.vpsLightbox a.closeButton');
        sleep(1);
        $this->assertNotVisible('css=.vpsLightbox');
    }

    public function testOriginal()
    {
        $this->openVpc('/foo4');
        $this->click('//a');
        $this->waitForConnections();
        $this->assertVisible('css=.vpsLightbox');
        $this->assertElementPresent('css=.vpsLightbox a.fullSizeLink');
    }

    public function testAdmin()
    {
        $this->openVpcEdit('Vpc_Basic_ImageEnlarge_TestComponent', 1);
        $this->waitForConnections();
        //test k�nnte nat�rlich verbessert werden, aber zumindest testen ob kein fehler kommt
    }
}
