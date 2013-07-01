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
        $this->_addImage(1);
        $this->_process();
        $html = $this->_root->render();
        $this->assertContains('kwcBasicImageCacheRootImagesEnlargeComponent', $html);
        $this->assertContains('kwcBasicImageCacheRootImageEnlargeComponent', $html);
        $this->assertNotContains('/media/Kwc_Basic_Image_Cache_Root_ImageEnlargeComponent', $html);

        // check uploaded
        $model = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_TestModel');
        $row = $model->getRow('root-1');
        $row->kwf_upload_id = 1;
        $row->dimension = 'original';
        $row->save();

        $this->_process();
        $html = $this->_root->render();

        $this->assertContains('kwcBasicImageCacheRootImagesEnlargeComponent', $html);
        $this->assertContains('kwcBasicImageCacheRootImageEnlargeComponent', $html);
        $this->assertRegExp("#/media/Kwc_Basic_Image_Cache_Root_ImageEnlargeComponent/root-1/default/[^/]+/[0-9]+/foo.png#ms", $html);
    }

    public function testNextPreviousLinks()
    {
        // Add 1 image and check that there is no link in lightbox
        $this->_addImage(1);
        $html = $this->_root->getChildComponent('-1')->getChildComponent('-linkTag')
            ->getChildComponent('_imagePage')->render();
        $this->assertNotContains('href="/kwf/kwctest/Kwc_Basic_Image_Cache_Root_ImagesEnlargeComponent', $html);

        // Add second image, there should be a next link in first image lightbox
        $this->_process();
        $this->_addImage(2);
        $html = $this->_root->getChildComponent('-1')->getChildComponent('-linkTag')
            ->getChildComponent('_imagePage')->render();
        $this->assertContains('href="/kwf/kwctest/Kwc_Basic_Image_Cache_Root_ImagesEnlargeComponent/2:image"', $html);

        // Hide second image, next link in first image lightbox should disappear
        $this->_process();
        $model = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_Cache_Root_ListModel');
        $row = $model->getRow(2);
        $row->visible = 0;
        $row->save();
        $this->_process();
        $html = $this->_root->getChildComponent('-1')->getChildComponent('-linkTag')
            ->getChildComponent('_imagePage')->render();
        $this->assertNotContains('href="/kwf/kwctest/Kwc_Basic_Image_Cache_Root_ImagesEnlargeComponent', $html);
    }

    private function _addImage($id)
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_Cache_Root_ListModel');
        $row = $model->createRow(array(
            'id' => $id,
            'component_id'=>'root',
            'pos'=>$id,
            'visible' => 1
        ));
        $row->save();

        $model = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_TestModel');
        $row = $model->createRow(array(
            'component_id'=>'root-' . $id,
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
            'component_id'=>'root-' . $id . '-linkTag',
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
    }
}
