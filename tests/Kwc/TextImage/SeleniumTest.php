<?php
/**
 * @group slow
 * @group selenium
 * @group Kwc_TextImage
 */
class Kwc_TextImage_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_TextImage_Root');
    }

    public function testLightbox()
    {
        $this->openKwc('/textimage1');
        sleep(5);
        $this->assertContainsText('css=.kwcTextImageTestComponent .kwcText', 'foo');
        sleep(5);
        $this->assertElementNotPresent('css=.kwfLightbox');
        $this->assertElementPresent('css=.kwcTextImageTestComponent a img');
        $this->click('css=.kwcTextImageTestComponent a img');
        $this->waitForConnections();
        $this->assertVisible('css=.kwfLightbox');
        $this->assertElementPresent('css=.kwfLightbox div.image img');
        $this->click('css=.kwfLightbox a.closeButton');
        sleep(1);
        $this->assertNotVisible('css=.kwfLightbox');
    }

    public function testAdmin()
    {
        $this->openKwcEdit('Kwc_TextImage_TestComponent', 'root_textImage1');
        sleep(5);
        $this->waitForConnections();
        //test könnte natürlich verbessert werden, aber zumindest testen ob kein fehler kommt
    }
}
