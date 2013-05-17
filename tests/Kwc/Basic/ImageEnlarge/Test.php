<?php
/**
 * @group Basic_ImageEnlarge
 * @group Kwc_Image
 *
 * http://kwf.niko.vivid/kwf/kwctest/Kwc_Basic_ImageEnlarge_Root/foo1
 */
class Kwc_Basic_ImageEnlarge_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_ImageEnlarge_Root');
        $this->_root->setFilename(null);
    }

    public function testWithoutSmallImageComponent()
    {
        $c = $this->_root->getComponentById('1800');
        $dim = $c->getComponent()->getImageDimensions();
        $this->assertEquals(10, $dim['width']);
        $this->assertEquals(10, $dim['height']);

        $url = explode('/', trim($c->getComponent()->getImageUrl(), '/'));
        $this->assertEquals('Kwc_Basic_ImageEnlarge_WithoutSmallImageComponent', $url[1]);
        $this->assertEquals('1800', $url[2]);
        $this->assertEquals('default', $url[3]);
    }

    public function testWithoutSmallImageComponentHtml()
    {
        $html = $this->_root->getComponentById(1800)->render();

        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($html);
        $xml = simplexml_import_dom($doc);

        $img = $xml->xpath("//img");
        $this->assertEquals(1, count($img));
        $this->assertEquals(10, (string)$img[0]['width']);
        $this->assertEquals(10, (string)$img[0]['height']);
        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', (string)$img[0]['src'], $m));
        $o = call_user_func(array($m[1], 'getMediaOutput'), $m[2], $m[3], $m[1]);
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        if (isset($o['contents'])) {
            $contents = $o['contents'];
        } else {
            $contents = file_get_contents($o['file']);
        }
        $im->readImageBlob($contents);
        $this->assertEquals(10, $im->getImageWidth());
        $this->assertEquals(10, $im->getImageHeight());
        $this->assertEquals(Kwf_Media_Image::scale(Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/1',
                                    array(10, 10, Kwf_Media_Image::SCALE_DEFORM)), $contents);

        $a = $xml->xpath("//a");
        $this->assertEquals(1, count($a));
        $this->assertEquals('lightbox{"width":16,"height":16,"style":"CenterBox","adaptHeight":true}', (string)$a[0]['rel']);
        $this->assertEquals('/foo1/image', (string)$a[0]['href']);


        $html = $this->_root->getComponentById('1800-linkTag_imagePage')->render();
        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($html);
        $xml = simplexml_import_dom($doc);

        $img = $xml->xpath("//img");
        $this->assertEquals(1, count($img));
        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', (string)$img[0]['src'], $m));
        $o = call_user_func(array($m[1], 'getMediaOutput'), $m[2], $m[3], $m[1]);
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        if (isset($o['contents'])) {
            $contents = $o['contents'];
        } else {
            $contents = file_get_contents($o['file']);
        }
        $im->readImageBlob($contents);
        $this->assertEquals(16, $im->getImageWidth());
        $this->assertEquals(16, $im->getImageHeight());
        $this->assertEquals(Kwf_Media_Image::scale(Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/1',
                                    array(16, 16, Kwf_Media_Image::SCALE_DEFORM)), $contents);
    }

    public function testWithoutSmallImageUploaded()
    {
        $c = $this->_root->getComponentById('1801');
        $dim = $c->getComponent()->getImageDimensions();
        $this->assertEquals(10, $dim['width']);
        $this->assertEquals(10, $dim['height']);

        $url = explode('/', trim($c->getComponent()->getImageUrl(), '/'));
        $this->assertEquals('Kwc_Basic_ImageEnlarge_TestComponent', $url[1]);
        $this->assertEquals('1801', $url[2]);
        $this->assertEquals('default', $url[3]);
    }

    public function testWithoutSmallImageUploadedHtml()
    {
        $html = $this->_root->getComponentById(1801)->render();

        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($html);
        $xml = simplexml_import_dom($doc);

        $img = $xml->xpath("//img");
        $this->assertEquals(1, count($img));
        $this->assertEquals(10, (string)$img[0]['width']);
        $this->assertEquals(10, (string)$img[0]['height']);
        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', (string)$img[0]['src'], $m));
        $o = call_user_func(array($m[1], 'getMediaOutput'), $m[2], $m[3], $m[1]);
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        if (isset($o['contents'])) {
            $contents = $o['contents'];
        } else {
            $contents = file_get_contents($o['file']);
        }
        $im->readImageBlob($contents);
        $this->assertEquals(10, $im->getImageWidth());
        $this->assertEquals(10, $im->getImageHeight());
        $this->assertEquals(Kwf_Media_Image::scale(Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/1',
                                    array(10, 10, Kwf_Media_Image::SCALE_DEFORM)), $contents);

        $a = $xml->xpath("//a");
        $this->assertEquals(1, count($a));
        $this->assertEquals('lightbox{"width":16,"height":16,"style":"CenterBox","adaptHeight":true}', (string)$a[0]['rel']);
        $this->assertEquals('/foo2/image', (string)$a[0]['href']);



        $html = $this->_root->getComponentById('1801-linkTag_imagePage')->render();
        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($html);
        $xml = simplexml_import_dom($doc);

        $img = $xml->xpath("//img");

        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', (string)$img[0]['src'], $m));
        $o = call_user_func(array($m[1], 'getMediaOutput'), $m[2], $m[3], $m[1]);
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        if (isset($o['contents'])) {
            $contents = $o['contents'];
        } else {
            $contents = file_get_contents($o['file']);
        }
        $im->readImageBlob($contents);
        $this->assertEquals(16, $im->getImageWidth());
        $this->assertEquals(16, $im->getImageHeight());
        $this->assertEquals(Kwf_Media_Image::scale(Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/1',
                                    array(16, 16, Kwf_Media_Image::SCALE_DEFORM)), $contents);
    }

    public function testWithSmallImageUploaded()
    {
        $c = $this->_root->getComponentById('1802');
        $dim = $c->getComponent()->getImageDimensions();
        $this->assertEquals(10, $dim['width']);
        $this->assertEquals(10, $dim['height']);
        $data = $c->getComponent()->getImageData();
        $this->assertEquals('1802-linkTag', $data['row']->component_id);

        $url = explode('/', trim($c->getComponent()->getImageUrl(), '/'));
        $this->assertEquals('Kwc_Basic_ImageEnlarge_TestComponent', $url[1]);
        $this->assertEquals('1802', $url[2]);
        $this->assertEquals('default', $url[3]);

        $c = $this->_root->getComponentById('1802-linkTag');
        $dim = $c->getComponent()->getImageDimensions();
        $this->assertEquals(210, $dim['width']);
        $this->assertEquals(70, $dim['height']);
        $data = $c->getComponent()->getImageData();
        $this->assertEquals('1802', $data['row']->component_id);
    }

    public function testWithSmallImageUploadedHtml()
    {
        $html = $this->_root->getComponentById(1802)->render();

        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($html);
        $xml = simplexml_import_dom($doc);

        $img = $xml->xpath("//img");
        $this->assertEquals(1, count($img));
        $this->assertEquals(10, (string)$img[0]['width']);
        $this->assertEquals(10, (string)$img[0]['height']);
        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', (string)$img[0]['src'], $m));
        $o = call_user_func(array($m[1], 'getMediaOutput'), $m[2], $m[3], $m[1]);
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        if (isset($o['contents'])) {
            $contents = $o['contents'];
        } else {
            $contents = file_get_contents($o['file']);
        }
        $im->readImageBlob($contents);
        $this->assertEquals(10, $im->getImageWidth());
        $this->assertEquals(10, $im->getImageHeight());
        $this->assertEquals(Kwf_Media_Image::scale(Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/1',
                                    array(10, 10, Kwf_Media_Image::SCALE_DEFORM)), $contents);

        $a = $xml->xpath("//a");
        $this->assertEquals(1, count($a));
        $this->assertEquals('lightbox{"width":210,"height":70,"style":"CenterBox","adaptHeight":true}', (string)$a[0]['rel']);
        $this->assertEquals('/foo3/image', (string)$a[0]['href']);


        $html = $this->_root->getComponentById('1802-linkTag_imagePage')->render();
        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($html);
        $xml = simplexml_import_dom($doc);

        $img = $xml->xpath("//img");

        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', (string)$img[0]['src'], $m));
        $o = call_user_func(array($m[1], 'getMediaOutput'), $m[2], $m[3], $m[1]);
        $this->assertEquals('image/gif', $o['mimeType']);
        $im = new Imagick();
        if (isset($o['contents'])) {
            $contents = $o['contents'];
        } else {
            $contents = file_get_contents($o['file']);
        }
        $im->readImageBlob($contents);
        $this->assertEquals(210, $im->getImageWidth());
        $this->assertEquals(70, $im->getImageHeight());
        $this->assertEquals(Kwf_Media_Image::scale(Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/2',
                                    array(270, 70, Kwf_Media_Image::SCALE_BESTFIT)), $contents);
    }

    public function testWithOriginalHtml()
    {
        $html = $this->_root->getComponentById('1803-linkTag_imagePage')->render();

        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($html);
        $xml = simplexml_import_dom($doc);

        $a = $xml->xpath("//p/a");
        $this->assertEquals(1, count($a));
        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', (string)$a[0]['href'], $m));
        $o = call_user_func(array($m[1], 'getMediaOutput'), $m[2], $m[3], $m[1]);
        $this->assertEquals('application/octet-stream', $o['mimeType']);
        $im = new Imagick();
        $this->assertEquals(Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/1', $o['file']);
    }
}
