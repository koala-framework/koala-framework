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
        $this->assertEquals(Kwf_Media_Image::scale(Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/'.$smallImage['uploadId'],
                                    array($smallImage['width'], $smallImage['height'], Kwf_Media_Image::SCALE_DEFORM)), $o['contents']);

        $a = $xml->xpath("//a");
        $this->assertEquals(1, count($a));
        $this->assertEquals('lightbox{"width":'.$largeImage['width'].',"height":'.$largeImage['height'].',"style":"CenterBox","adaptHeight":true}', (string)$a[0]['rel']);
        $this->assertEquals($largeImage['pageUrl'], (string)$a[0]['href']);


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
        $this->assertEquals(Kwf_Media_Image::scale(Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/'.$largeImage['uploadId'],
                                    array($largeImage['width'], $largeImage['height'], Kwf_Media_Image::SCALE_DEFORM)), file_get_contents($o['file']));
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

    public function testWithoutSmallImageComponentAddSmall()
    {
        $this->_assert(
            '1800',
            array('width'=>10, 'height'=>10, 'uploadId'=>1, 'mimeType' => 'image/png'),
            array('width'=>16, 'height'=>16, 'uploadId'=>1, 'mimeType' => 'image/png', 'pageUrl'=>'/foo1/image')
        );

        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_TestModel')->createRow();
        $row->component_id = '1800-linkTag';
        $row->kwf_upload_id = 2;
        $row->preview_image = 1;
        $row->save();
        $this->_process();

        $this->_assert(
            '1800',
            array('width'=>10, 'height'=>10, 'uploadId'=>2, 'mimeType' => 'image/gif'),
            array('width'=>16, 'height'=>16, 'uploadId'=>1, 'mimeType' => 'image/png', 'pageUrl'=>'/foo1/image')
        );
    }

    public function testWithSmallImageComponentRemoveSmall1()
    {
        $this->_assert(
            '1802',
            array('width'=>10, 'height'=>10, 'uploadId'=>1, 'mimeType' => 'image/png'),
            array('width'=>210, 'height'=>70, 'uploadId'=>2, 'mimeType' => 'image/gif', 'pageUrl'=>'/foo3/image')
        );

        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_TestModel')->getRow('1802-linkTag');
        $row->preview_image = 0;
        $row->save();
        $this->_process();

        $this->_assert(
            '1802',
            array('width'=>10, 'height'=>10, 'uploadId'=>2, 'mimeType' => 'image/gif'),
            array('width'=>210, 'height'=>70, 'uploadId'=>2, 'mimeType' => 'image/gif', 'pageUrl'=>'/foo3/image')
        );
    }

    public function testWithSmallImageComponentRemoveSmall2()
    {
        $this->_assert(
            '1802',
            array('width'=>10, 'height'=>10, 'uploadId'=>1, 'mimeType' => 'image/png'),
            array('width'=>210, 'height'=>70, 'uploadId'=>2, 'mimeType' => 'image/gif', 'pageUrl'=>'/foo3/image')
        );

        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_TestModel')->getRow('1802-linkTag');
        $row->kwf_upload_id = null;
        $row->save();
        $this->_process();

        $this->_assert(
            '1802',
            array('width'=>10, 'height'=>10, 'uploadId'=>2, 'mimeType' => 'image/gif'),
            array('width'=>210, 'height'=>70, 'uploadId'=>2, 'mimeType' => 'image/gif', 'pageUrl'=>'/foo3/image')
        );
    }

    public function testWithSmallImageComponentChangeSmall()
    {
        $this->_assert(
            '1802',
            array('width'=>10, 'height'=>10, 'uploadId'=>1, 'mimeType' => 'image/png'),
            array('width'=>210, 'height'=>70, 'uploadId'=>2, 'mimeType' => 'image/gif', 'pageUrl'=>'/foo3/image')
        );

        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_ImageEnlarge_TestModel')->getRow('1802-linkTag');
        $row->kwf_upload_id = 3;
        $row->save();
        $this->_process();

        $this->_assert(
            '1802',
            array('width'=>10, 'height'=>10, 'uploadId'=>3, 'mimeType' => 'image/png'),
            array('width'=>210, 'height'=>70, 'uploadId'=>2, 'mimeType' => 'image/gif', 'pageUrl'=>'/foo3/image')
        );
    }

}
