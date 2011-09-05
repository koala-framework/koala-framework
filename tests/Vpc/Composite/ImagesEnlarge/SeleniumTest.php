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
        $this->assertVisible('css=.lightbox');
        $this->assertElementPresent('css=.lightbox img.centerImage');
        $this->assertElementPresent('css=.lightbox a.nextSwitchButton');
        $this->assertElementNotPresent('css=.lightbox a.previousSwitchButton');
        $this->click('css=.lightbox a.nextSwitchButton');
        $this->assertElementPresent('css=.lightbox a.nextSwitchButton');
        $this->assertElementPresent('css=.lightbox a.previousSwitchButton');
        $this->click('css=.lightbox a.nextSwitchButton');
        $this->assertElementNotPresent('css=.lightbox a.nextSwitchButton');
        $this->assertElementPresent('css=.lightbox a.previousSwitchButton');
        $this->click('css=.lightbox a.previousSwitchButton');
        $this->assertElementPresent('css=.lightbox a.nextSwitchButton');
        $this->assertElementPresent('css=.lightbox a.previousSwitchButton');
    }
}
