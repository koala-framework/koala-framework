<?php
/**
 * @group Kwc_Image
 */
class Kwc_Basic_ImageEnlarge_CacheTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_ImageEnlarge_Root');
        $this->_root->setFilename(null);
    }

    public function testWithoutSmallImageComponentHtml()
    {
        $html = $this->_root->getComponentById('1800')->render();

        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($html);
        $xml = simplexml_import_dom($doc);

        $img = $xml->xpath("//img");
        $this->assertEquals(1, count($img));
        $this->assertEquals(10, (string)$img[0]['width']);
        $this->assertEquals(10, (string)$img[0]['height']);
        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', (string)$img[0]['src'], $m));
        $o = Kwf_Media::getOutput($m[1], $m[2], $m[3]);
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(10, $im->getImageWidth());
        $this->assertEquals(10, $im->getImageHeight());
        $this->assertEquals(Kwf_Media_Image::scale(Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/1',
                                    array(10, 10, Kwf_Media_Image::SCALE_DEFORM)), $o['contents']);

        $a = $xml->xpath("//a");
        $this->assertEquals(1, count($a));
        $this->assertEquals('lightbox{"width":16,"height":16,"style":"CenterBox"}', (string)$a[0]['rel']);
        $this->assertEquals('/foo1/image', (string)$a[0]['href']);


        $html = $this->_root->getComponentById('1800-linkTag_imagePage')->render();
        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($html);
        $xml = simplexml_import_dom($doc);

        $img = $xml->xpath("//img");
        $this->assertEquals(1, count($img));
        $this->assertEquals(16, (string)$img[0]['width']);
        $this->assertEquals(16, (string)$img[0]['height']);
        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', (string)$img[0]['src'], $m));
        $o = Kwf_Media::getOutput($m[1], $m[2], $m[3]);
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        $im->readImage($o['file']);
        $this->assertEquals(16, $im->getImageWidth());
        $this->assertEquals(16, $im->getImageHeight());
        $this->assertEquals(Kwf_Media_Image::scale(Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/1',
                                    array(16, 16, Kwf_Media_Image::SCALE_DEFORM)), file_get_contents($o['file']));

        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_TestModel')->getRow('1800');
        $row->kwf_upload_id = 2;
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('1800')->render();

        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($html);
        $xml = simplexml_import_dom($doc);

        $img = $xml->xpath("//img");
        $this->assertEquals(1, count($img));
        $this->assertEquals(10, (string)$img[0]['width']);
        $this->assertEquals(10, (string)$img[0]['height']);
        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', (string)$img[0]['src'], $m));
        $o = Kwf_Media::getOutput($m[1], $m[2], $m[3]);
        $this->assertEquals('image/gif', $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(10, $im->getImageWidth());
        $this->assertEquals(10, $im->getImageHeight());
        $this->assertEquals(Kwf_Media_Image::scale(Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/2',
                                    array(10, 10, Kwf_Media_Image::SCALE_DEFORM)), $o['contents']);

        $a = $xml->xpath("//a");
        $this->assertEquals(1, count($a));
        $this->assertEquals('lightbox{"width":210,"height":70,"style":"CenterBox"}', (string)$a[0]['rel']);
        $this->assertEquals('/foo1/image', (string)$a[0]['href']);


        $html = $this->_root->getComponentById('1800-linkTag_imagePage')->render();
        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($html);
        $xml = simplexml_import_dom($doc);

        $img = $xml->xpath("//img");
        $this->assertEquals(1, count($img));
        $this->assertEquals(210, (string)$img[0]['width']);
        $this->assertEquals(70, (string)$img[0]['height']);
        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', (string)$img[0]['src'], $m));
        $o = Kwf_Media::getOutput($m[1], $m[2], $m[3]);
        $this->assertEquals('image/gif', $o['mimeType']);
        $im = new Imagick();
        $im->readImage($o['file']);
        $this->assertEquals(210, $im->getImageWidth());
        $this->assertEquals(70, $im->getImageHeight());
        $this->assertEquals(Kwf_Media_Image::scale(Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/2',
                                    array(210, 70, Kwf_Media_Image::SCALE_DEFORM)), file_get_contents($o['file']));
        
    }
}
