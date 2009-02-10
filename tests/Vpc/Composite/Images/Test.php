<?php
/**
 * @group Composite_Images
 */
class Vpc_Composite_Images_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Composite_Images_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testHtml()
    {
        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($this->_root->getComponentById(2100));
        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($html);
        $xml = simplexml_import_dom($doc);

        $img = $xml->xpath("//img");
        $this->assertEquals(3, count($img));
        $this->assertEquals(100, (string)$img[0]['width']);
        $this->assertEquals(100, (string)$img[0]['height']);
        $src = (string)$img[0]['src'];

        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', (string)$img[0]['src'], $m));
        $o = call_user_func(array($m[1], 'getMediaOutput'), $m[2], $m[3], $m[1]);
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(100, $im->getImageWidth());
        $this->assertEquals(100, $im->getImageHeight());
        $this->assertEquals(Vps_Media_Image::scale(Vps_Model_Abstract::getInstance('Vpc_Composite_Images_Image_UploadsModel')->getUploadDir().'/1',
                                    array(100, 100, Vps_Media_Image::SCALE_CROP)), $o['contents']);
    }

}
