<?php
/**
 * @group slow
 * @group selenium
 * @group Kwc_Image
 * @group Image
 */
class Kwc_ImageResponsive_CreatesImgElement_Test extends Kwf_Test_SeleniumTestCase
{
    /**
     * Use this links to test components for responsiveness:
     * This is a manually test. Please copy urls into browser and check result
     * and especially behaviour for different screensizes
     *
     * Abstract-Image-Component:
     *   http://kwf.benjamin.vivid/kwf/kwctest/Kwc_ImageResponsive_CreatesImgElement_Root_Component/imageabstract1
     *   http://kwf.benjamin.vivid/kwf/kwctest/Kwc_ImageResponsive_CreatesImgElement_Root_Component/imageabstract2
     *   http://kwf.benjamin.vivid/kwf/kwctest/Kwc_ImageResponsive_CreatesImgElement_Root_Component/imageabstract3
     *   http://kwf.benjamin.vivid/kwf/kwctest/Kwc_ImageResponsive_CreatesImgElement_Root_Component/imageabstract4
     *
     * Basic-Image-Component:
     *   http://kwf.benjamin.vivid/kwf/kwctest/Kwc_ImageResponsive_CreatesImgElement_Root_Component/imagebasic1
     *
     * Basic-Image-Enlarge-Component:
     *   http://kwf.benjamin.vivid/kwf/kwctest/Kwc_ImageResponsive_CreatesImgElement_Root_Component/imageenlarge1
     *
     * TextImage-Component:
     *   http://kwf.benjamin.vivid/kwf/kwctest/Kwc_ImageResponsive_CreatesImgElement_Root_Component/textimage1
     */
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_ImageResponsive_CreatesImgElement_Root_Component');
    }

    /**
     * Kwc_Abstract_Image_Component
     * Kwc_Basic_Text_Image_Component
     * Kwc_TextImage_Component
     * Kwc_Basic_ImageEnlarge_EnlargeTag_Component
     */

    public function testJavascriptCreatesCorrectImageSrcElement()
    {
        $this->openKwc('/imageabstract1');
        sleep(2);
        $this->assertElementPresent("css=img[src^=\"/kwf/kwctest/Kwc_ImageResponsive_CreatesImgElement_Root_Component/media/Kwc_ImageResponsive_CreatesImgElement_Components_ImageAbstract_Component/root_imageabstract1/dh-\"]");
    }

    public function testJavascriptCreatesCorrectImageSrcImageEnlargeImagePage()
    {
        $this->openKwc('/imageenlarge1');
        $this->click("css=.kwcBasicImageEnlarge > a");

        $this->waitForConnections();
        $this->assertElementPresent("css=.kwcBasicImageEnlargeEnlargeTagImagePage img[src^=\"/kwf/kwctest/Kwc_ImageResponsive_CreatesImgElement_Root_Component/media/Kwc_ImageResponsive_CreatesImgElement_Components_ImageEnlarge_EnlargeTag_Component/root_imageenlarge1-linkTag/dh-\"]");
    }
}
