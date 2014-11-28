<?php
/**
 * @group slow
 * @group selenium
 * @group Kwc_Image
 * @group Image
 */
class Kwc_ImageResponsive_InScrollDiv_Test extends Kwf_Test_SeleniumTestCase
{
    /**
     *   http://kwf.niko.vivid/kwf/kwctest/Kwc_ImageResponsive_InScrollDiv_Root_Component/image1
     */
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_ImageResponsive_InScrollDiv_Root_Component');
    }

    public function testJavascriptCreatesCorrectImageSrcElement()
    {
        $this->openKwc('/image1');
        sleep(2);
        //scroll down to make image visible
        $this->getEval('selenium.browserbot.getCurrentWindow().document.getElementById(\'scrollContainer\').scrollTop = 1000;');
        sleep(1);
        //must be loaded now
        //(current implementation always loads it)
        $this->assertElementPresent("css=img[src^=\"/kwf/kwctest/Kwc_ImageResponsive_InScrollDiv_Root_Component/media/Kwc_ImageResponsive_InScrollDiv_Components_Image_Component/root_image1\"]");
    }
}
