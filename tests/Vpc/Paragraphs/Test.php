<?php
/**
 * @group Vpc_Paragraphs
 */
class Vpc_Paragraphs_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Paragraphs_Paragraphs');
    }

    public function testCache()
    {
        $model = $this->_root->getComponent()->getModel();

        $this->assertEquals(1, substr_count($this->_root->render(), 'foo'));

        // Zeile in Paragraphs hinzufügen udn alles resetten
        $row = $model->createRow(
            array('id' => 3, 'component_id'=>'root', 'pos'=>3, 'visible' => 1, 'component' => 'paragraph')
        );
        $row->save();
        $this->_process();
        $this->assertEquals(2, substr_count($this->_root->render(), 'foo'));

        // Zeile löschen
        $model->getRow(3)->delete();
        $this->_process();
        $this->assertEquals(1, substr_count($this->_root->render(), 'foo'));

        // Status auf true setzen
        $row = $model->getRow(2);
        $row->visible = 1;
        $row->save();
        $this->_process();
        $this->assertEquals(2, substr_count($this->_root->render(), 'foo'));
    }

    public function testWriteCache()
    {
        $cacheModel = Vps_Component_Cache::getInstance()->getModel();

        $this->assertNull($cacheModel->getRow('root'));
        $this->_root->render();
        $this->assertNotNull($cacheModel->getRow('root'));
    }

    public function testCacheVars()
    {
        $cacheVars = $this->_root->getComponent()->getCacheVars();
        $this->assertEquals(1, count($cacheVars));
        $this->assertEquals('Vpc_Paragraphs_ParagraphsModel', get_class($cacheVars[0]['model']));
        $this->assertNull($cacheVars[0]['id']);
    }
}