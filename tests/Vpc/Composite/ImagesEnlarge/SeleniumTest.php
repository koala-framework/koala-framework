<?php
/**
 * @group slow
 * @group Composite_ImagesEnlarge
 */
class Vpc_Composite_ImagesEnlarge_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Component_Data_Root::setComponentClass('Vpc_Composite_ImagesEnlarge_Root');
    }

    public function tearDown()
    {
        $m = Vps_Model_Abstract::getInstance('Vpc_Composite_ImagesEnlarge_ImageEnlarge_UploadsModel');
        $dir = $m->getUploadDir();
        if (substr($dir, 0, 4)=='/tmp') {
            system('rm -r '.$dir);
        }
    }

    public function testNextPrevLink()
    {
        $this->openVpc('/foo');
        $this->assertElementPresent('css=.vpcCompositeImagesEnlarge a img');
        $this->click('css=a');
        $this->assertVisible('css=.lightbox');
        $this->assertElementPresent('css=.lightbox .lightboxBody img');
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
