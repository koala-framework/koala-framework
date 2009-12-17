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
        $this->assertNotVisible('css=.lightbox');
        $this->assertElementPresent('css=.vpcBasicImageEnlargeWithoutSmallImageComponent a img');
        $this->click('//a');
        $this->assertVisible('css=.lightbox');
        $this->assertElementPresent('css=.lightbox img.centerImage');
        $this->click('css=.lightbox a.closeButton');
        $this->assertNotVisible('css=.lightbox');
    }

    public function testOriginal()
    {
        $this->openVpc('/foo4');
        $this->click('//a');
        $this->assertVisible('css=.lightbox');
        $this->assertElementPresent('css=.lightbox a.fullSizeLink');
    }

    public function testAdmin()
    {
        $this->openVpcEdit('Vpc_Basic_ImageEnlarge_TestComponent', 1);
        //test k�nnte nat�rlich verbessert werden, aber zumindest testen ob kein fehler kommt
    }
}
