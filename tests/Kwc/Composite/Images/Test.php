<?php
/**
 * @group Composite_Images
 *
 * http://kwf.kwf.niko.vivid/kwf/componentedittest/Kwc_Composite_Images_Root/Kwc_Composite_Images_TestComponent/Index?componentId=2100
 */
class Kwc_Composite_Images_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Composite_Images_Root');
    }

    public function testHtml()
    {
        $html = $this->_root->getComponentById(2100)->render();
        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($html);
        $xml = simplexml_import_dom($doc);

        $img = $xml->xpath("//img");
        $this->assertEquals(3, count($img));
        $this->assertEquals(100, (string)$img[0]['width']);
        $this->assertEquals(100, (string)$img[0]['height']);
        $src = (string)$img[0]['src'];

        $this->assertTrue(!!preg_match('#/media/([^/]+)/([^/]+)/([^/]+)#', (string)$img[0]['src'], $m));
        $o = call_user_func(array($m[1], 'getMediaOutput'), $m[2], $m[3], $m[1]);
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(100, $im->getImageWidth());
        $this->assertEquals(100, $im->getImageHeight());
        $this->assertEquals(Kwf_Media_Image::scale(Kwf_Model_Abstract::getInstance('Kwc_Composite_Images_Image_UploadsModel')->getUploadDir().'/1',
                                    array(100, 100, 'cover' => true)), $o['contents']);
    }

}
