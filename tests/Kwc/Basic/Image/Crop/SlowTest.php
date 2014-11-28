<?php
/**
 * @group Basic_ImageEnlarge
 * @group Kwc_Image
 * @group Image
 * @group slow
 *
 * http://kwf.niko.vivid/kwf/kwctest/Kwc_Basic_Image_Crop_Root/page
 */
class Kwc_Basic_Image_Crop_SlowTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_Image_Crop_Root');
        $this->_root->setFilename(null);
    }

    public function testUrlChangeWhenChangingCropOptions()
    {
        $component = $this->_root->getComponentById('root_page');

        $url1 = $component->getComponent()->getBaseImageUrl();
        $html1 = $component->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_Crop_TestModel')->getRow('root_page');
        $row->crop_x = 20;
        $row->save();
        $this->_process();
        sleep(1);

        $url2 = $component->getComponent()->getBaseImageUrl();
        $html2 = $component->render();

        $this->assertNotEquals($url1, $url2);
        $this->assertNotEquals($html1, $html2);
    }
}
