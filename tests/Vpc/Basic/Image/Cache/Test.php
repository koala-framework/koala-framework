<?php
/**
 * @group Basic_Image
 * @group ImageCache
 * @group Image
 */
class Vpc_Basic_Image_Cache_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_Image_Cache_Root_ImagesEnlargeComponent');
    }

    public function testCacheClearing()
    {
        $this->_process();

        // check empty
        $html = $this->_root->render();
        $this->assertContains('vpcBasicImageCacheRootImagesEnlargeComponent', $html);
        $this->assertNotContains('vpcBasicImageCacheRootImageEnlargeComponent', $html);

        // check testModel row with no upload_id
        $model = Vps_Model_Abstract::getInstance('Vpc_Basic_Image_Cache_Root_ListModel');
        $row = $model->createRow(array(
            'id' => 1,
            'component_id'=>'root',
            'pos'=>1,
            'visible' => 1
        ));
        $row->save();

        $model = Vps_Model_Abstract::getInstance('Vpc_Basic_Image_TestModel');
        $row = $model->createRow(array(
            'component_id'=>'root-1',
            'filename' => null,
            'comment' => null,
            'width' => null,
            'height' => null,
            'enlarge' => 0,
            'vps_upload_id'=>null,
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
            'vps_upload_id'=>null,
            'dimension' => null,
            'preview_image' => '',
            'title' => ''
        ));
        $row2->save();

        $this->_process();
        $html = $this->_root->render();
        $this->assertContains('vpcBasicImageCacheRootImagesEnlargeComponent', $html);
        $this->assertContains('vpcBasicImageCacheRootImageEnlargeComponent', $html);
        $this->assertNotContains('/media/Vpc_Basic_Image_Cache_Root_ImageEnlargeComponent', $html);

        // check uploaded
        $row->vps_upload_id = 1;
        $row->dimension = 'original';
        $row->save();

        $this->_process();
        $html = $this->_root->render();

        $this->assertContains('vpcBasicImageCacheRootImagesEnlargeComponent', $html);
        $this->assertContains('vpcBasicImageCacheRootImageEnlargeComponent', $html);
        $this->assertRegExp("#/media/Vpc_Basic_Image_Cache_Root_ImageEnlargeComponent/root-1/default/[^/]+/[0-9]+/foo.png#ms", $html);
    }
}
