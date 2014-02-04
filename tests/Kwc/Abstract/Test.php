<?php
class Kwc_Abstract_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Abstract_Root_Component');
    }

    public function testImageCacheDeletedAfterDimensionsChange()
    {
        $html = $this->_root->getComponentById('root_imageabstract1')->render();
        // Normal size
        $file200 = Kwf_Media::getOutput('Kwc_Abstract_Image_TestComponent', 'root_imageabstract1', 'dh-200');
        // Small size
        $file100 = Kwf_Media::getOutput('Kwc_Abstract_Image_TestComponent', 'root_imageabstract1', 'dh-100');

        $model = Kwf_Model_Abstract::getInstance('Kwc_Abstract_Image_TestModel');
        $row = $model->getRow('root_imageabstract1');
        $row->dimension = 'default2';
        $row->kwf_upload_id = 2;
        $row->save();
        $this->_process();
        // cache for dh-100 and dh-200 must be deleted, dh-300 and dh-400 remain as zombie

        $html2 = $this->_root->getComponentById('root_imageabstract1')->render();
        $this->assertNotEquals($html, $html2);

        // Dpr2 size
        $otherfile200 = Kwf_Media::getOutput('Kwc_Abstract_Image_TestComponent', 'root_imageabstract1', 'dh-200');
        // Normal size
        $otherfile100 = Kwf_Media::getOutput('Kwc_Abstract_Image_TestComponent', 'root_imageabstract1', 'dh-100');

        // SmallSize cache-id and normal-size cache-id could colide
        $this->assertNotEquals($file100, $otherfile100);
        // NormalSize cache-id and Dpr2-size cache-id could colide
        $this->assertNotEquals($file200, $otherfile200);
    }
}
