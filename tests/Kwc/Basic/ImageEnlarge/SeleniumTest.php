<?php
/**
 * @group slow
 * @group selenium
 * @group Basic_ImageEnlarge
 * @group Image
 */
class Kwc_Basic_ImageEnlarge_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_Basic_ImageEnlarge_Root');
    }

    public function testLightbox()
    {
        $this->openKwc('/foo1');
        $this->assertElementNotPresent('css=.kwfLightbox');
        $this->assertElementPresent('css=.kwcBasicImageEnlargeWithoutSmallImageComponent a img');
        $this->click('//a');
        $this->waitForConnections();
        $this->assertVisible('css=.kwfLightbox');
        $this->assertElementPresent('css=.kwfLightbox div.image');
        $this->assertElementPresent('css=.kwfLightbox div.image img');
        $this->click('css=.kwfLightbox a.closeButton');
        //sleep(3);
        //commented, test fails randomly $this->assertNotVisible('css=.kwfLightbox');
    }

    public function testOriginal()
    {
        $this->openKwc('/foo4');
        $this->click('//a');
        $this->waitForConnections();
        $this->assertVisible('css=.kwfLightbox');
        $this->assertElementPresent('css=.kwfLightbox a.fullSizeLink');
    }

    public function testAdmin()
    {
        $this->openKwcEdit('Kwc_Basic_ImageEnlarge_TestComponent', 1);
        $this->waitForConnections();
        //test k�nnte nat�rlich verbessert werden, aber zumindest testen ob kein fehler kommt
    }
}
