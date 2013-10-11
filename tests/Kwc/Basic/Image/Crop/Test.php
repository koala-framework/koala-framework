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

    public function testClearOutputCacheWhenChangingCropOptions()
    {
        Kwf_Media::clearCache('Kwc_Basic_Image_Crop_ImageComponent', 'root_page', 'default');

        Kwc_Basic_Image_Crop_ImageComponent::$getMediaOutputCalled = 0;

        Kwf_Media::getOutput('Kwc_Basic_Image_Crop_ImageComponent', 'root_page', 'default');
        $this->assertEquals(1, Kwc_Basic_Image_Crop_ImageComponent::$getMediaOutputCalled);

        Kwf_Media::getOutput('Kwc_Basic_Image_Crop_ImageComponent', 'root_page', 'default');
        $this->assertEquals(1, Kwc_Basic_Image_Crop_ImageComponent::$getMediaOutputCalled);

        $c = $this->_root->getComponentById('root_page');
        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_Crop_TestModel')->getRow('root_page');
        $row->crop_x = 20;
        $row->save();
        Kwf_Component_ModelObserver::getInstance()->process();
        Kwf_Media::getOutput('Kwc_Basic_Image_Crop_ImageComponent', 'root_page', 'default');
        $this->assertEquals(2, Kwc_Basic_Image_Crop_ImageComponent::$getMediaOutputCalled);
    }

    public function testSettingCropOptions()
    {
        $c = $this->_root->getComponentById('root_page');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(500, $s['width']);
        $this->assertEquals(112, $s['height']);
        $this->assertEquals(14, $s['crop']['x']);
        $this->assertEquals(820, $s['crop']['y']);
        $this->assertEquals(2012, $s['crop']['width']);
        $this->assertEquals(450, $s['crop']['height']);
    }
}
