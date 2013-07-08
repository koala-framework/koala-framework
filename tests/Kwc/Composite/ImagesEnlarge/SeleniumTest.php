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
        $this->assertElementPresent('css=.kwcCompositeImagesEnlargeImageEnlargeTestComponent a img');
        $this->click('css=.kwcCompositeImagesEnlargeImageEnlargeTestComponent a');
        $this->waitForConnections();
        sleep(1);
        $this->assertElementPresent('css=.kwfLightboxOpen');
        $this->assertVisible('css=.kwfLightboxOpen');
        $this->assertElementPresent('css=.kwfLightboxOpen div.image');
        $this->assertElementPresent('css=.kwfLightboxOpen div.image img');
        $this->assertElementPresent('css=.kwfLightboxOpen .nextBtn a');
        $this->assertElementNotPresent('css=.kwfLightboxOpen .prevBtn a');
        $this->click('css=.kwfLightboxOpen .nextBtn a');
        sleep(1);
        $this->assertElementPresent('css=.kwfLightboxOpen .nextBtn a');
        $this->assertElementPresent('css=.kwfLightboxOpen .prevBtn a');
        $this->click('css=.kwfLightboxOpen .nextBtn a');
        sleep(1);
        $this->assertElementNotPresent('css=.kwfLightboxOpen .nextBtn a');
        $this->assertElementPresent('css=.kwfLightboxOpen .prevBtn a');
        $this->click('css=.kwfLightboxOpen .prevBtn a');
        sleep(1);
        $this->assertElementPresent('css=.kwfLightboxOpen .nextBtn a');
        $this->assertElementPresent('css=.kwfLightboxOpen .prevBtn a');
    }
}
