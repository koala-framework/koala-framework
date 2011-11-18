<?php
/**
 * @group Kwc_Image
 */
class Kwc_Basic_Image_Cache_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_Image_Cache_Root_ImagesEnlargeComponent');
    }

    public function testCacheClearing()
    {
        $this->_process();

        // check empty
        $html = $this->_root->render();
        $this->assertContains('kwcBasicImageCacheRootImagesEnlargeComponent', $html);
        $this->assertNotContains('kwcBasicImageCacheRootImageEnlargeComponent', $html);

        // check testModel row with no upload_id
        $model = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_Cache_Root_ListModel');
        $row = $model->createRow(array(
            'id' => 1,
            'component_id'=>'root',
            'pos'=>1,
            'visible' => 1
        ));
        $row->save();

        $model = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_TestModel');
        $row = $model->createRow(array(
            'component_id'=>'root-1',
            'filename' => null,
            'comment' => null,
            'width' => null,
            'height' => null,
            'enlarge' => 0,
            'kwf_upload_id'=>null,
            'dimension' => 'default'
        ));
        $row->save();
        $row2 = $model->createRow(array(
            'component_id'=>'root-1-linkTag',
            'filename' => null,
            'comment' => null,
            'width' => null,
            'height' => null,
            'enlarge' => 0,
            'kwf_upload_id'=>null,
            'dimension' => null,
            'preview_image' => '',
            'title' => ''
        ));
        $row2->save();

        $this->_process();
        $html = $this->_root->render();
        $this->assertContains('kwcBasicImageCacheRootImagesEnlargeComponent', $html);
        $this->assertContains('kwcBasicImageCacheRootImageEnlargeComponent', $html);
        $this->assertNotContains('/media/Kwc_Basic_Image_Cache_Root_ImageEnlargeComponent', $html);

        // check uploaded
        $row->kwf_upload_id = 1;
        $row->dimension = 'original';
        $row->save();

        $this->_process();
        $html = $this->_root->render();

        $this->assertContains('kwcBasicImageCacheRootImagesEnlargeComponent', $html);
        $this->assertContains('kwcBasicImageCacheRootImageEnlargeComponent', $html);
        $this->assertRegExp("#/media/Kwc_Basic_Image_Cache_Root_ImageEnlargeComponent/root-1/default/[^/]+/[0-9]+/foo.png#ms", $html);
    }
}
