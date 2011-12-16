<?php
/**
 * @group Kwc_Image
 */
class Kwc_Basic_Image_CacheParentImage_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_Image_CacheParentImage_Root_Component');
    }

    public function testImage()
    {
        $c = $this->_root->getComponentById('root_image');
        $html = $c->render(true, false);
        $this->assertTrue(!!preg_match('#<img src="([^"]+)" width="(\d+)" height="(\d+)"#', $html, $m));
        $this->assertEquals($m[2], 10);
        $this->assertEquals($m[3], 10);
        $url = $m[1];
        $this->assertTrue(!!preg_match('#/media/([^/]+)/([^/]+)/([^/]+)#', $url, $m));
        $this->assertEquals($m[1], 'Kwc_Basic_Image_CacheParentImage_Image_Component');
        $this->assertEquals($m[2], 'root_image');
        $this->assertEquals($m[3], 'default');
        $o = Kwf_Media::getOutput($m[1], $m[2], $m[3]);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(10, $im->getImageWidth());
        $this->assertEquals(10, $im->getImageHeight());

        $model = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_CacheParentImage_Image_TestModel');
        $row = $model->getRow('root_image');
        $row->kwf_upload_id = 2;
        $row->save();

        $this->_process();

        $c = $this->_root->getComponentById('root_image');
        $html = $c->render(true, false);
        $this->assertTrue(!!preg_match('#<img src="([^"]+)" width="(\d+)" height="(\d+)"#', $html, $m));
        $this->assertEquals($m[2], 10);
        $this->assertEquals($m[3], 3);
        $url = $m[1];
        $this->assertTrue(!!preg_match('#/media/([^/]+)/([^/]+)/([^/]+)#', $url, $m));
        $this->assertEquals($m[1], 'Kwc_Basic_Image_CacheParentImage_Image_Component');
        $this->assertEquals($m[2], 'root_image');
        $this->assertEquals($m[3], 'default');
        $o = Kwf_Media::getOutput($m[1], $m[2], $m[3]);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(10, $im->getImageWidth());
        $this->assertEquals(3, $im->getImageHeight());
    }

    public function testParentImage()
    {
        $c = $this->_root->getComponentById('root_image-parentImage');
        $html = $c->render(true, false);
        $this->assertTrue(!!preg_match('#<img src="([^"]+)" width="(\d+)" height="(\d+)"#', $html, $m));
        $this->assertEquals($m[2], 20);
        $this->assertEquals($m[3], 20);
        $url = $m[1];
        $this->assertTrue(!!preg_match('#/media/([^/]+)/([^/]+)/([^/]+)#', $url, $m));
        $this->assertEquals($m[1], 'Kwc_Basic_Image_CacheParentImage_ParentImage_Component');
        $this->assertEquals($m[2], 'root_image-parentImage');
        $this->assertEquals($m[3], 'default');
        $o = Kwf_Media::getOutput($m[1], $m[2], $m[3]);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(20, $im->getImageWidth());
        $this->assertEquals(20, $im->getImageHeight());

        $model = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_CacheParentImage_Image_TestModel');
        $row = $model->getRow('root_image');
        $row->kwf_upload_id = 2;
        $row->save();

        $this->_process();

        $c = $this->_root->getComponentById('root_image-parentImage');
        $html = $c->render(true, false);
        $this->assertTrue(!!preg_match('#<img src="([^"]+)" width="(\d+)" height="(\d+)"#', $html, $m));
        $this->assertEquals($m[2], 20);
        $this->assertEquals($m[3], 7);
        $url = $m[1];
        $this->assertTrue(!!preg_match('#/media/([^/]+)/([^/]+)/([^/]+)#', $url, $m));
        $this->assertEquals($m[1], 'Kwc_Basic_Image_CacheParentImage_ParentImage_Component');
        $this->assertEquals($m[2], 'root_image-parentImage');
        $this->assertEquals($m[3], 'default');
        $o = Kwf_Media::getOutput($m[1], $m[2], $m[3]);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(20, $im->getImageWidth());
        $this->assertEquals(7, $im->getImageHeight());
    }
}
