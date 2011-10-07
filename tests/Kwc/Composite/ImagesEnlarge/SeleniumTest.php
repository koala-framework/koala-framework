<?php
/**
 * @group slow
 * @group selenium
 * @group Composite_ImagesEnlarge
 */
class Kwc_Composite_ImagesEnlarge_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_Composite_ImagesEnlarge_Root');
    }

    public function testNextPrevLink()
    {
        $this->openKwc('/foo');
        $this->assertElementPresent('css=.kwcCompositeImagesEnlarge a img');
        $this->click('css=a');
        $this->waitForConnections();
        $this->assertElementPresent('css=.kwfLightboxOpen');
        $this->assertVisible('css=.kwfLightboxOpen');
        $this->assertElementPresent('css=.kwfLightboxOpen div.image');
        $this->assertElementPresent('css=.kwfLightboxOpen div.image img');
        $this->assertElementPresent('css=.kwfLightboxOpen .nextBtn a');
        $this->assertElementNotPresent('css=.kwfLightboxOpen .prevBtn a');
        $this->click('css=.kwfLightboxOpen .nextBtn a');
        $this->assertElementPresent('css=.kwfLightboxOpen .nextBtn a');
        $this->assertElementPresent('css=.kwfLightboxOpen .prevBtn a');
        $this->click('css=.kwfLightboxOpen .nextBtn a');
        $this->assertElementNotPresent('css=.kwfLightboxOpen .nextBtn a');
        $this->assertElementPresent('css=.kwfLightboxOpen .prevBtn a');
        $this->click('css=.kwfLightboxOpen .prevBtn a');
        $this->assertElementPresent('css=.kwfLightboxOpen .nextBtn a');
        $this->assertElementPresent('css=.kwfLightboxOpen .prevBtn a');
    }
}
