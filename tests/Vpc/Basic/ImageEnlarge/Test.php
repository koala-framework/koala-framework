<?php
/**
 * @group Basic_ImageEnlarge
 */
class Vpc_Basic_ImageEnlarge_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Basic_ImageEnlarge_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testWithoutSmallImageComponent()
    {
        $c = $this->_root->getComponentById('1800');
        $url = explode('/', trim($c->getComponent()->getImageUrl(), '/'));
        $this->assertEquals('Vpc_Basic_ImageEnlarge_WithoutSmallImageComponent', $url[1]);
        $this->assertEquals('1800', $url[2]);
        $this->assertEquals('default', $url[3]);

        $smallImage = $c->getComponent()->getSmallImage();
        $this->assertEquals('10', $smallImage['width']);
        $this->assertEquals('10', $smallImage['height']);
        $url = explode('/', trim($smallImage['url'], '/'));
        $this->assertEquals('Vpc_Basic_ImageEnlarge_SmallImage_Component', $url[1]);
        $this->assertEquals('1800', $url[2]);
        $this->assertEquals('default', $url[3]);
    }

    public function testWithoutSmallImageComponentHtml()
    {
        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($this->_root->getComponentById(1800));
        $this->assertContains('<a href="/media/Vpc_Basic_ImageEnlarge_WithoutSmallImageComponent/1800/default/', $html);
        $this->assertContains('rel="enlarge_16_16"', $html);
        $this->assertContains('<img src="/media/Vpc_Basic_ImageEnlarge_SmallImage_Component/1800/default/', $html);
        $this->assertContains('height="10"', $html);
        $this->assertContains('width="10"', $html);
    }

    public function testWithoutSmallImageComponentGetMediaOutput()
    {
        $o = Vpc_Basic_ImageEnlarge_WithoutSmallImageComponent::getMediaOutput('1800', 'default', 'Vpc_Basic_ImageEnlarge_WithoutSmallImageComponent');
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(16, $im->getImageWidth());
        $this->assertEquals(16, $im->getImageHeight());
        $this->assertEquals(Vps_Media_Image::scale(Vps_Model_Abstract::getInstance('Vpc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/1',
                                    array(16, 16, Vps_Media_Image::SCALE_DEFORM)), $o['contents']);

        $o = Vpc_Basic_ImageEnlarge_WithoutSmallImageComponent::getMediaOutput('1800', 'default', 'Vpc_Basic_ImageEnlarge_SmallImage_Component');
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(10, $im->getImageWidth());
        $this->assertEquals(10, $im->getImageHeight());
        $this->assertEquals(Vps_Media_Image::scale(Vps_Model_Abstract::getInstance('Vpc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/1',
                                    array(10, 10, Vps_Media_Image::SCALE_DEFORM)), $o['contents']);
    }

    public function testWithoutSmallImageUploaded()
    {
        $c = $this->_root->getComponentById('1801');
        $url = explode('/', trim($c->getComponent()->getImageUrl(), '/'));
        $this->assertEquals('Vpc_Basic_ImageEnlarge_TestComponent', $url[1]);
        $this->assertEquals('1801', $url[2]);
        $this->assertEquals('default', $url[3]);

        $smallImage = $c->getComponent()->getSmallImage();
        $this->assertEquals('10', $smallImage['width']);
        $this->assertEquals('10', $smallImage['height']);
        $url = explode('/', trim($smallImage['url'], '/'));
        $this->assertEquals('Vpc_Basic_ImageEnlarge_SmallImage_Component', $url[1]);
        $this->assertEquals('1801', $url[2]);
        $this->assertEquals('default', $url[3]);
    }

    public function testWithoutSmallImageUploadedGetMediaOutput()
    {
        $o = Vpc_Basic_ImageEnlarge_WithoutSmallImageComponent::getMediaOutput('1801', 'default', 'Vpc_Basic_ImageEnlarge_SmallImage_Component');
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(10, $im->getImageWidth());
        $this->assertEquals(10, $im->getImageHeight());
    }

    public function testWithSmallImageUploaded()
    {
        $c = $this->_root->getComponentById('1802');
        $url = explode('/', trim($c->getComponent()->getImageUrl(), '/'));
        $this->assertEquals('Vpc_Basic_ImageEnlarge_TestComponent', $url[1]);
        $this->assertEquals('1802', $url[2]);
        $this->assertEquals('default', $url[3]);

        $smallImage = $c->getComponent()->getSmallImage();
        $this->assertEquals('10', $smallImage['width']);
        $this->assertEquals('10', $smallImage['height']);
        $url = explode('/', trim($smallImage['url'], '/'));
        $this->assertEquals('Vpc_Basic_ImageEnlarge_SmallImage_Component', $url[1]);
        $this->assertEquals('1802-smallImage', $url[2]);
        $this->assertEquals('default', $url[3]);
    }

    public function testWithSmallImageUploadedGetMediaOutput()
    {
        $o = Vpc_Basic_ImageEnlarge_WithoutSmallImageComponent::getMediaOutput('1802-smallImage', 'default', 'Vpc_Basic_ImageEnlarge_SmallImage_Component');
        $this->assertEquals('image/gif', $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(10, $im->getImageWidth());
        $this->assertEquals(10, $im->getImageHeight());
        $this->assertEquals(Vps_Media_Image::scale(Vps_Model_Abstract::getInstance('Vpc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/2',
                                    array(10, 10, Vps_Media_Image::SCALE_DEFORM)), $o['contents']);
    }

    public function testOriginalImageHtml()
    {
        $output = new Vps_Component_Output_NoCache();
        $html = $output->render($this->_root->getComponentById(1803));
        $this->assertContains('<a href="/media/Vpc_Basic_ImageEnlarge_OriginalImageComponent/1803/default/', $html);
        $this->assertContains('rel="enlarge_16_16_/media/Vpc_Basic_ImageEnlarge_OriginalImageComponent/1803/original/', $html);
        $this->assertContains('<img src="/media/Vpc_Basic_ImageEnlarge_SmallImage_Component/1803/default/', $html);
        $this->assertContains('height="10"', $html);
        $this->assertContains('width="10"', $html);
    }

    public function testOriginalImageGetMediaOutput()
    {
        $o = Vpc_Basic_ImageEnlarge_OriginalImageComponent::getMediaOutput('1803', 'original', 'Vpc_Basic_ImageEnlarge_OriginalImageComponent');
        $this->assertEquals('image/png', $o['mimeType']);
        $this->assertEquals(Vps_Model_Abstract::getInstance('Vpc_Basic_ImageEnlarge_UploadsModel')->getUploadDir().'/1', $o['file']);
    }
}
