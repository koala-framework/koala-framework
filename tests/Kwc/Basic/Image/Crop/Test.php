<?php
/**
 * @group Basic_ImageEnlarge
 * @group Kwc_Image
 *
 * http://kwf.niko.vivid/kwf/kwctest/Kwc_Basic_Image_Crop_Root/page
 */
class Kwc_Basic_Image_Crop_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_Image_Crop_Root');
        $this->_root->setFilename(null);
    }

    public function testBestFitWithZeroHeight()
    {
        $c = $this->_root->getChildComponent('_page');
        $this->assertTrue($c->hasContent());
        $this->assertEquals(array('width'=>500, 'height'=>112, 'rotate' => null,
            'crop' => array(
                'x' => 14,
                'y' => 820,
                'width' => 2012,
                'height' => 450
            )
        ),
        $c->getComponent()->getImageDimensions());
    }

    public function testFixDimension()
    {
        $c = $this->_root->getChildComponent('_page1');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(500, $s['width']);
        $this->assertEquals(500, $s['height']);
    }

    public function testGetMediaOutput()
    {
        $o = Kwc_Basic_Image_Component::getMediaOutput('root_page', 'default', 'Kwc_Basic_Image_Crop_ImageComponent');
        $this->assertEquals('image/jpg', $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(500, $im->getImageWidth());
        $this->assertEquals(112, $im->getImageHeight());
    }

    public function testGetMediaOutputFixDimension()
    {
        $o = Kwc_Basic_Image_Component::getMediaOutput('root_page1', 'default', 'Kwc_Basic_Image_Crop_ImageComponent');
        $this->assertEquals('image/jpg', $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(500, $im->getImageWidth());
        $this->assertEquals(500, $im->getImageHeight());
    }

    public function testHtml()
    {
        $html = $this->_root->getChildComponent('_page')->render();
        $this->assertRegExp('#^\s*<div class="kwcAbstractImage kwcBasicImageCropImageComponent".*>'.
            '\s*<img src="/media/Kwc_Basic_Image_Crop_ImageComponent/root_page/default/[^/]+/[0-9]+/foo2.jpg" width="500" height="112" alt="" />'.
            '\s*</div>\s*$#ms', $html);
    }

    public function testHtmlFixDimension()
    {
        $html = $this->_root->getChildComponent('_page1')->render();
        $this->assertRegExp('#^\s*<div class="kwcAbstractImage kwcBasicImageCropImageFixDimensionComponent".*>'.
            '\s*<img src="/media/Kwc_Basic_Image_Crop_ImageFixDimensionComponent/root_page1/default/[^/]+/[0-9]+/foo2.jpg" width="500" height="500" alt="" />'.
            '\s*</div>\s*$#ms', $html);
    }

    public function testParentImage()
    {
        $c = $this->_root->getComponentById('root_page10-child');
        $this->assertTrue($c->hasContent());
        $url = $c->getComponent()->getImageUrl();
        $this->assertNotNull($url);
        $url = explode('/', trim($url, '/'));
        $class = $url[1];
        $id = $url[2];
        $type = $url[3];

        $o = Kwc_Basic_Image_Component::getMediaOutput($id, $type, $class);
        $this->assertNotNull($o);
        $this->assertEquals('image/jpg', $o['mimeType']);
        $im = new Imagick();
        $im->readImageBlob($o['contents']);
        $this->assertEquals(16, $im->getImageWidth());
        $this->assertEquals(16, $im->getImageHeight());

        Kwf_Media::getOutput('Kwc_Basic_Image_Crop_ParentImage_Child_Component', 'root_page10-child', 'default');
        $c = $this->_root->getComponentById('root_page10');
        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_Crop_TestModel')->getRow('root_page10');
        $row->kwf_upload_id = 2;
        $row->save();
        Kwf_Component_ModelObserver::getInstance()->process();
        Kwf_Media::getOutput('Kwc_Basic_Image_Crop_ParentImage_Child_Component', 'root_page10-child', 'default');
        $this->assertEquals(2, Kwc_Basic_Image_Crop_ParentImage_Child_Component::$getMediaOutputCalled);
    }

    public function testDimensionSetByRow()
    {
        $c = $this->_root->getChildComponent('_page2');
        $this->assertEquals(array('width'=>100, 'height'=>22, 'rotate' => null,
            'crop' => array(
                'x' => 14,
                'y' => 820,
                'width' => 2012,
                'height' => 450
            )
        ),
        $c->getComponent()->getImageDimensions());
    }

    public function testClearOutputCache()
    {
        Kwf_Media::clearCache('Kwc_Basic_Image_Crop_ImageFixDimensionComponent', 'root_page1', 'default');

        Kwc_Basic_Image_Crop_ImageFixDimensionComponent::$getMediaOutputCalled = 0;

        Kwf_Media::getOutput('Kwc_Basic_Image_Crop_ImageFixDimensionComponent', 'root_page1', 'default');
        $this->assertEquals(1, Kwc_Basic_Image_Crop_ImageFixDimensionComponent::$getMediaOutputCalled);

        Kwf_Media::getOutput('Kwc_Basic_Image_Crop_ImageFixDimensionComponent', 'root_page1', 'default');
        $this->assertEquals(1, Kwc_Basic_Image_Crop_ImageFixDimensionComponent::$getMediaOutputCalled);

        Kwf_Media::clearCache('Kwc_Basic_Image_Crop_ImageFixDimensionComponent', 'root_page1', 'default');
        Kwf_Media::getOutput('Kwc_Basic_Image_Crop_ImageFixDimensionComponent', 'root_page1', 'default');
        $this->assertEquals(2, Kwc_Basic_Image_Crop_ImageFixDimensionComponent::$getMediaOutputCalled);

        $c = $this->_root->getComponentById('root_page1');
        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_Crop_TestModel')->getRow('root_page1');
        $row->kwf_upload_id = 2;
        $row->save();
        Kwf_Component_ModelObserver::getInstance()->process();
        Kwf_Media::getOutput('Kwc_Basic_Image_Crop_ImageFixDimensionComponent', 'root_page1', 'default');
        $this->assertEquals(3, Kwc_Basic_Image_Crop_ImageFixDimensionComponent::$getMediaOutputCalled);
    }

    public function testMultipleDimensions()
    {
        $c = $this->_root->getComponentById('root_page3');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(100, $s['width']);
        $this->assertEquals(100, $s['height']);

        $c = $this->_root->getComponentById('root_page4');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(200, $s['width']);
        $this->assertEquals(200, $s['height']);

        $c = $this->_root->getComponentById('root_page5');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(2040, $s['width']);
        $this->assertEquals(1336, $s['height']);

        $c = $this->_root->getComponentById('root_page6');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(50, $s['width']);
        $this->assertEquals(300, $s['height']);

        $c = $this->_root->getComponentById('root_page7');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(300, $s['width']);
        $this->assertEquals(50, $s['height']);

        $c = $this->_root->getComponentById('root_page8');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(50, $s['width']);
        $this->assertEquals(50, $s['height']);

        $c = $this->_root->getComponentById('root_page9');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(300, $s['height']);
    }
}
