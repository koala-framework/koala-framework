<?php
class Kwc_Basic_Image_DprImage_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_Image_DprImage_Root');
    }

    //http://kwf.niko.vivid/kwf/kwctest/Kwc_Basic_Image_DprImage_Root/page1
    //http://kwf.niko.vivid/kwf/kwctest/Kwc_Basic_Image_DprImage_Root/page2
    //the actual replacement (JS) can't be tested in a unittest as we don't run them on retina displays

    public function testClearCache()
    {
        $c = $this->_root->getComponentById('root_page1');
        $html = $c->render();
        $this->assertTrue(!!preg_match('#data-src="([^"]+)"#', $html, $m));
        $url = str_replace('{width}', '', $m[1]);
        $url = explode('/', trim($url, '/'));
        $class = $url[4];
        $id = $url[5];
        $type = $url[6];

        $o = Kwf_Media::getOutput($class, $id, $type);
        $this->assertNotNull($o);
        $this->assertEquals('image/jpg', $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(26, $im->getImageWidth());
        $this->assertEquals(32, $im->getImageHeight());

        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_DprImage_TestModel')->getRow('root_page1');
        $row->kwf_upload_id = 3;
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root_page1');
        $html = $c->render();
        $this->assertTrue(!!preg_match('#data-src="([^"]+)"#', $html, $m));
        $url = str_replace('{width}', '', $m[1]);
        $url = explode('/', trim($url, '/'));
        $class = $url[4];
        $id = $url[5];
        $type = $url[6];

        $o = Kwf_Media::getOutput($class, $id, $type);
        $this->assertNotNull($o);
        $this->assertEquals('image/gif', $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(32, $im->getImageWidth());
        $this->assertEquals(11, $im->getImageHeight());
    }
}
