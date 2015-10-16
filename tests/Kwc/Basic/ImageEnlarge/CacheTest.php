<?php
/**
 * @group Kwc_Image
 * @group Image
 */
class Kwc_Basic_ImageEnlarge_CacheTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_ImageEnlarge_Root');
        $this->_root->setFilename(null);
    }

    private function _assert($componentId, $smallImage, $largeImage)
    {
        $html = $this->_root->getComponentById($componentId)->render();

        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($html);
        $xml = simplexml_import_dom($doc);

        $img = $xml->xpath("//img");
        $this->assertEquals(1, count($img));
        $this->assertEquals($smallImage['width'], (string)$img[0]['width']);
        $this->assertEquals($smallImage['height'], (string)$img[0]['height']);
        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', (string)$img[0]['src'], $m));
        $o = Kwf_Media::getOutput($m[1], $m[2], $m[3]);
        $this->assertEquals($smallImage['mimeType'], $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals($smallImage['width'], $im->getImageWidth());
        $this->assertEquals($smallImage['height'], $im->getImageHeight());
        $this->assertEquals(Kwf_Media_Image::scale(Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_UploadsModel')->getRow($smallImage['uploadId'])->getFileSource(),
                                    array('width'=>$smallImage['width'], 'height'=>$smallImage['height'], 'cover' => true), $smallImage['uploadId']), $o['contents']);

        $a = $xml->xpath("//a");
        $this->assertEquals(1, count($a));
        $this->assertEquals('{"width":'.$largeImage['width'].',"height":'.$largeImage['height'].',"style":"CenterBox","adaptHeight":true,"lightboxUrl":"\/foo1\/image"}', (string)$a[0]['data-kwc-lightbox']);
        $this->assertContains('/media/Kwc_Basic_ImageEnlarge_EnlargeTagWithoutSmall_TestComponent/1800-linkTag', (string)$a[0]['href']);


        $html = $this->_root->getComponentById($componentId.'-linkTag_imagePage')->render();
        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($html);
        $xml = simplexml_import_dom($doc);

        $img = $xml->xpath("//img");
        $this->assertEquals(1, count($img));
        $this->assertEquals($largeImage['width'], (string)$img[0]['width']);
        $this->assertEquals($largeImage['height'], (string)$img[0]['height']);
        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', (string)$img[0]['src'], $m));
        $o = Kwf_Media::getOutput($m[1], $m[2], $m[3]);
        $this->assertEquals($largeImage['mimeType'], $o['mimeType']);
        $im = new Imagick();
        $im->readImage($o['file']);
        $this->assertEquals($largeImage['width'], $im->getImageWidth());
        $this->assertEquals($largeImage['height'], $im->getImageHeight());
        $this->assertEquals(Kwf_Media_Image::scale(Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_UploadsModel')->getRow($largeImage['uploadId'])->getFileSource(),
                                    array($largeImage['width'], $largeImage['height'], 'cover' => true)), file_get_contents($o['file']));
    }

    public function testWithoutSmallImageComponentHtml()
    {
        $this->_assert(
            '1800',
            array('width'=>10, 'height'=>10, 'uploadId'=>1, 'mimeType' => 'image/png'),
            array('width'=>16, 'height'=>16, 'uploadId'=>1, 'mimeType' => 'image/png', 'pageUrl'=>'/foo1/image')
        );

        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_TestModel')->getRow('1800');
        $row->kwf_upload_id = 2;
        $row->save();
        $this->_process();

        $this->_assert(
            '1800',
            array('width'=>10, 'height'=>10, 'uploadId'=>2, 'mimeType' => 'image/gif'),
            array('width'=>210, 'height'=>70, 'uploadId'=>2, 'mimeType' => 'image/gif', 'pageUrl'=>'/foo1/image')
        );
    }

    public function testEnlargeCacheDeletedOnBaseImageChanged()
    {
        // Image and Enlarge have to define different dimensions because else it
        // could happen that the parent has the same types as the child.
        // Get EnlargeComponent
        $component = $this->_root->getChildComponent('1804')
                                 ->getChildComponent('-linkTag')
                                 ->getChildComponent('_imagePage');
        // Render EnlargeComponent. Request Enlarge-Image (image has to be big enough)
        preg_match_all('#/media/([^/]+)/([^/]+)/([^/]+)#', $component->render(), $matches);
        foreach ($matches[0] as $key => $m) {
            if (strpos($matches[3][$key], '{width}')!==false) continue;
            $fileWithGreaterHeight = Kwf_Media::getOutput($matches[1][$key], $matches[2][$key], $matches[3][$key]);
        }

        // Change basis-bild
        $row = $this->_root->getChildComponent('1804')->getComponent()->getRow();
        $row->kwf_upload_id = 5;
        $row->save();
        $this->_process();

        // Assert if image cache was changed
        preg_match_all('#/media/([^/]+)/([^/]+)/([^/]+)#', $component->render(), $matches);
        foreach ($matches[0] as $key => $m) {
            if (strpos($matches[3][$key], '{width}')!==false) continue;
            $fileWithSmallerHeight = Kwf_Media::getOutput($matches[1][$key], $matches[2][$key], $matches[3][$key]);
        }
        $image1 = new Imagick($fileWithGreaterHeight['file']);
        $image2 = new Imagick($fileWithSmallerHeight['file']);
        $this->assertNotEquals($image1->getImageHeight(), $image2->getImageHeight());
    }
}
