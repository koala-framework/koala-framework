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
    }

    public function testUrl()
    {
//         $rows = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_UploadsModel')->getRows();
//         d($rows);
//         d($this->_root->getChildComponent('_page')->getComponent()->getImageUrl());
        d($this->_root->getChildComponent('_page')->getComponent()->getRow());//->getParentRow('Image'));
    }
}
