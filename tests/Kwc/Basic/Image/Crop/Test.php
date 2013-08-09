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

    public function testUrl()
    {
        $this->markTestIncomplete();
//         $rows = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_UploadsModel')->getRows();
//         d($rows);
//         d($this->_root->getChildComponent('_page')->getComponent()->getImageUrl());
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
        $this->markTestIncomplete();
        $html = $this->_root->getChildComponent('_page')->render();
        $this->assertRegExp('#^\s*<div class="kwcAbstractImage kwcBasicImageCropComponent".*>'.
            '\s*<img src="/media/Kwc_Basic_Image_Crop_ImageComponent/root_page/default/[^/]+/[0-9]+/foo2.jpg" width="500" height="112" alt="" />'.
            '\s*</div>\s*$#ms', $html);
    }

    public function testHtmlFixDimension()
    {
        $this->markTestIncomplete();
        $html = $this->_root->getChildComponent('_page1')->render();

        $this->assertRegExp('#^\s*<div class="kwcAbstractImage kwcBasicImageCropComponent".*>'.
            '\s*<img src="/media/Kwc_Basic_Image_Crop_ImageComponent/root_page/default/[^/]+/[0-9]+/foo2.jpg" width="500" height="500" alt="" />'.
            '\s*</div>\s*$#ms', $html);
    }

    public function testParentImage()
    {
        $this->markTestIncomplete();
        $c = $this->_root->getComponentById('1605-child');
        $this->assertTrue($c->hasContent());
        $url = $c->getComponent()->getImageUrl();
        $this->assertNotNull($url);
        $url = explode('/', trim($url, '/'));
        $class = $url[1];
        $id = $url[2];
        $type = $url[3];

        $o = Kwc_Basic_Image_Component::getMediaOutput($id, $type, $class);
        $this->assertNotNull($o);
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        $im->readImage($o['file']);
        $this->assertEquals(16, $im->getImageWidth());
        $this->assertEquals(16, $im->getImageHeight());

        Kwf_Media::getOutput('Kwc_Basic_Image_ParentImageComponent_Child_Component', '1605-child', 'default');
        $c = $this->_root->getComponentById('1605');
        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_TestModel')->getRow('1605');
        $row->kwf_upload_id = 2;
        $row->save();
        Kwf_Component_ModelObserver::getInstance()->process();
        Kwf_Media::getOutput('Kwc_Basic_Image_ParentImageComponent_Child_Component', '1605-child', 'default');
        $this->assertEquals(2, Kwc_Basic_Image_ParentImageComponent_Child_Component::$getMediaOutputCalled);
    }

    public function testDimensionSetByRow()
    {
        $this->markTestIncomplete();
        $c = $this->_root->getChildComponent('_page');

        $this->assertEquals(array('width'=>10, 'height'=>10, 'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 16,
                'height' => 16
            )
        ),
        $c->getComponent()->getImageDimensions());
    }

    public function testClearOutputCache()
    {
        $this->markTestIncomplete();
        Kwf_Media::clearCache('Kwc_Basic_Image_FixDimensionComponent', '1600', 'default');

        Kwc_Basic_Image_FixDimensionComponent::$getMediaOutputCalled = 0;

        Kwf_Media::getOutput('Kwc_Basic_Image_FixDimensionComponent', '1600', 'default');
        $this->assertEquals(1, Kwc_Basic_Image_FixDimensionComponent::$getMediaOutputCalled);

        Kwf_Media::getOutput('Kwc_Basic_Image_FixDimensionComponent', '1600', 'default');
        $this->assertEquals(1, Kwc_Basic_Image_FixDimensionComponent::$getMediaOutputCalled);

        Kwf_Media::clearCache('Kwc_Basic_Image_FixDimensionComponent', '1600', 'default');
        Kwf_Media::getOutput('Kwc_Basic_Image_FixDimensionComponent', '1600', 'default');
        $this->assertEquals(2, Kwc_Basic_Image_FixDimensionComponent::$getMediaOutputCalled);

        $c = $this->_root->getComponentById('1600');
        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_TestModel')->getRow('1600');
        $row->kwf_upload_id = 2;
        $row->save();
        Kwf_Component_ModelObserver::getInstance()->process();
        Kwf_Media::getOutput('Kwc_Basic_Image_FixDimensionComponent', '1600', 'default');
        $this->assertEquals(3, Kwc_Basic_Image_FixDimensionComponent::$getMediaOutputCalled);
    }

    public function testMultipleDimensions()
    {
        $this->markTestIncomplete();
        $c = $this->_root->getComponentById('1607');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(100, $s['width']);
        $this->assertEquals(100, $s['height']);

        $c = $this->_root->getComponentById('1608');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(200, $s['width']);
        $this->assertEquals(200, $s['height']);

        $c = $this->_root->getComponentById('1609');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(16, $s['width']);
        $this->assertEquals(16, $s['height']);

        $c = $this->_root->getComponentById('1610');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(50, $s['width']);
        $this->assertEquals(300, $s['height']);

        $c = $this->_root->getComponentById('1611');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(300, $s['width']);
        $this->assertEquals(50, $s['height']);

        $c = $this->_root->getComponentById('1612');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(50, $s['width']);
        $this->assertEquals(50, $s['height']);

        $c = $this->_root->getComponentById('1614');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(300, $s['width']); //correct?
        $this->assertEquals(300, $s['height']);
/*
        $c = $this->_root->getComponentById('1615');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(300, $s['width']); //correct?
        $this->assertEquals(300, $s['height']); //correct?

        $c = $this->_root->getComponentById('1616');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(300, $s['width']); //correct?
        $this->assertEquals(300, $s['height']); //correct?
*/
    }
}
