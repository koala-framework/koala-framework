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
        $p = $this->_root;
        $model = $p->getComponent()->getChildModel();

        $this->assertEquals(1, substr_count($p->render(), 'foo'));

        // Zeile in Paragraphs hinzufügen und alles resetten
        $row = $model->createRow(
            array('id' => 3, 'component_id'=>'root', 'pos'=>3, 'visible' => 1, 'component' => 'paragraph')
        );
        $row->save();
        $this->_process();
        $this->assertEquals(2, substr_count($p->render(), 'foo'));

        // Zeile löschen
        $model->getRow(3)->delete();
        $this->_process();
        $this->assertEquals(1, substr_count($p->render(), 'foo'));

        // Status auf true setzen
        $row = $model->getRow(2);
        $row->visible = 1;
        $row->save();
        $this->_process();
        $this->assertEquals(2, substr_count($p->render(), 'foo'));
    }

    public function testWriteCache()
    {
        $p = $this->_root;

        $cache = Vps_Component_Cache::getInstance();
        $cacheModel = $cache->getModel();

        // Cache wird geschrieben
        $this->assertNull($cache->load($this->_root));
        $this->assertEquals(0, $cacheModel->getRows()->count());
        $p->render();
        $this->assertNotNull($cache->load($this->_root));
        $this->assertEquals(3, $cacheModel->getRows()->count());

        // Row, die nicht zum aktuellen Paragraphs gehört, speichern, Cache darf nicht gelöscht werden
        $p->getComponent()->getChildModel()->getRow(11)->save();
        $this->_process();
        $this->assertNotNull($cache->load($this->_root));
        $this->assertEquals(3, $cacheModel->getRows()->count());

        // Eigene Row speichern, Cache muss gelöscht werden
        $p->getComponent()->getChildModel()->getRow(2)->save();
        $this->_process();
        $this->assertNull($cache->load($this->_root));
        $this->assertEquals(2, $cacheModel->getRows()->count());
    }
}
