<?php
/**
 * @group Kwc_Image
 */
class Kwc_Basic_Image_CacheFullWidth_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_Image_CacheFullWidth_Root_Component');
        $this->_root->setFilename(null);
    }

    public function testInitialImageSize()
    {
        $c = $this->_root->getComponentById(1);
        $html = $c->render(true, false);
        $this->assertContains('width="600" height="600"', $html);
        $this->assertTrue(!!preg_match('#src="(.+?)"#', $html, $m));
        $src = $m[1];

        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', $src, $m));
        $o = call_user_func(array($m[1], 'getMediaOutput'), $m[2], $m[3], $m[1]);
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(600, $im->getImageWidth());
        $this->assertEquals(600, $im->getImageHeight());
    }

    public function testBoxChangeHasContent()
    {
        $this->_root->getComponentById(1)->render(true, false);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_CacheFullWidth_Box_TestModel')->createRow();
        $row->component_id = '1-box';
        $row->content = 'asdf';
        $row->save();

        $this->_process();

        $html = $this->_root->getComponentById(1)->render(true, false);
        $this->assertContains('width="500" height="500"', $html);
        $this->assertTrue(!!preg_match('#src="(.+?)"#', $html, $m));
        $src = $m[1];

        $this->assertTrue(!!preg_match('#^/media/([^/]+)/([^/]+)/([^/]+)#', $src, $m));
        $o = call_user_func(array($m[1], 'getMediaOutput'), $m[2], $m[3], $m[1]);
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(500, $im->getImageWidth());
        $this->assertEquals(500, $im->getImageHeight());
    }
}
