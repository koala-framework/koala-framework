<?php
/**
 * @group Basic_Image
 * @group ImageCacheSimple
 * @group Image
 */
class Vpc_Basic_Image_CacheSimple_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_Image_CacheSimple_ImageComponent');
    }

    public function testCacheClearing()
    {
        // check empty
        $cache = Vps_Component_Cache::getInstance();
        $cacheModel = Vps_Component_Cache::getInstance()->getModel();
        $this->assertNull($cache->load($this->_root));
        $this->_root->render(true);
        $this->assertNotNull($cache->load($this->_root));
    }

    public function testRender()
    {
        // check testModel row with no upload_id
        $model = Vps_Model_Abstract::getInstance('Vpc_Basic_Image_TestModel');
        $row = $model->createRow(array(
            'component_id'=>'root',
            'filename' => null,
            'comment' => null,
            'width' => null,
            'height' => null,
            'enlarge' => 0,
            'vps_upload_id'=>null,
            'dimension' => 'default'
        ));
        $row->save();

        $this->_process();
        $html = $this->_root->render();
        $this->assertNotContains('<img', $html);

        // check uploaded
        $row->vps_upload_id = 1;
        $row->dimension = 'original';
        $row->save();

        $this->_process();
        $html = $this->_root->render();
        $this->assertContains('<img', $html);
    }
}
