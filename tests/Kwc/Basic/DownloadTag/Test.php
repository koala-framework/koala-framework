<?php
/**
 * @group Basic_DownloadTag
 */
class Kwc_Basic_DownloadTag_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_DownloadTag_Root');
    }

    public function testUrl()
    {
        $c = $this->_root->getComponentById('1700');
        $this->assertTrue($c->hasContent());

        $url = $c->getComponent()->getDownloadUrl();
        $url = explode('/', trim($url, '/'));
        $this->assertEquals('Kwc_Basic_DownloadTag_TestComponent', $url[1+3]);
        $this->assertEquals('1700', $url[2+3]);
        $this->assertEquals('default', $url[3+3]);
        $this->assertEquals('foo.png', $url[6+3]);
    }

    public function testUrlWithOwnFilename()
    {
        $c = $this->_root->getComponentById('1701');
        $url = $c->getComponent()->getDownloadUrl();
        $url = explode('/', trim($url, '/'));
        $this->assertEquals('myname.png', $url[6+3]);
    }

    public function testGetMediaOutput()
    {
        $o = Kwc_Basic_DownloadTag_Component::getMediaOutput('1700', 'default', 'Kwc_Basic_DownloadTag_TestComponent');
        $this->assertEquals('image/png', $o['mimeType']);
        $m = Kwf_Model_Abstract::getInstance('Kwc_ImageResponsive_MediaOutput_Image_UploadsModel');
        $this->assertTrue(file_exists($o['file']));
    }

    public function testHtml()
    {
        $html = $this->_root->getComponentById(1700)->render();
        $this->assertRegExp('#^<a .*?href="/kwf/kwctest/Kwc_Basic_DownloadTag_Root/media/Kwc_Basic_DownloadTag_TestComponent/1700/default/[^/]+/[0-9]+/foo.png" data-kwc-popup="blank">$#ms', $html);
    }

    public function testEmpty()
    {
        $c = $this->_root->getComponentById('1702');
        $this->assertFalse($c->hasContent());

        $this->assertEquals('', $c->render());
    }
}
