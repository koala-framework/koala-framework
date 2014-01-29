<?php
class Kwc_ImageResponsive_Test extends Kwc_TestAbstract
{
    /**
     * Use this links to test components for responsiveness:
     * This is a manually test. Please copy urls into browser and check result
     * and especially behaviour for different screensizes
     *
     * Abstract-Image-Component:
     *   http://kwf.benjamin.vivid/kwf/kwctest/Kwc_ImageResponsive_Root_Component/imageabstract1
     *   http://kwf.benjamin.vivid/kwf/kwctest/Kwc_ImageResponsive_Root_Component/imageabstract2
     *   http://kwf.benjamin.vivid/kwf/kwctest/Kwc_ImageResponsive_Root_Component/imageabstract3
     *   http://kwf.benjamin.vivid/kwf/kwctest/Kwc_ImageResponsive_Root_Component/imageabstract4
     *
     * Basic-Image-Component:
     *   http://kwf.benjamin.vivid/kwf/kwctest/Kwc_ImageResponsive_Root_Component/imagebasic1
     *
     * Basic-Image-Enlarge-Component:
     *   http://kwf.benjamin.vivid/kwf/kwctest/Kwc_ImageResponsive_Root_Component/imageenlarge1
     *
     * TextImage-Component:
     *   http://kwf.benjamin.vivid/kwf/kwctest/Kwc_ImageResponsive_Root_Component/textimage1
     */
    public function setUp()
    {
        parent::setUp('Kwc_ImageResponsive_Root_Component');
    }

    /**
     * Kwc_Abstract_Image_Component
     * Kwc_Basic_Text_Image_Component
     * Kwc_TextImage_Component
     * Kwc_Basic_ImageEnlarge_EnlargeTag_Component
     */

    public function testNothing()
    {
    }
}
