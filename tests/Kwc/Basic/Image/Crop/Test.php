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
