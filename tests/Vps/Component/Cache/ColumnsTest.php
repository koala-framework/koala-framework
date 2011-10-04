<?php
/**
 * @group Component_Cache
 * @group Component_CacheColumns
 */
class Vps_Component_Cache_ColumnsTest extends Vpc_TestAbstract
{
    private $_dataModel;
    private $_cacheModel;

    public function setUp()
    {
        parent::setUp('Vpc_Root_Component');

        $cache = Vps_Component_Cache::getInstance();
        $cacheModel = $cache->getModel('cache');

        // Cache-Row einfügen
        $cacheModel->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('component_id' => '1', 'db_id' => '1', 'type' => 'component', 'component_class' => 'Vpc_Foo'),
            array('component_id' => '2', 'db_id' => '2', 'type' => 'component', 'component_class' => 'Vpc_Foo'),
        ));
        $this->_cacheModel = $cacheModel;

        // Meta-Row einfügen
        $meta = new Vps_Component_Cache_Meta_Static_Model('Vps_Model_FnF', '{component_id}');
        $meta->setColumns(array('foo'));
        $cache->saveMeta('Vpc_Foo', $meta);

        // Datenmodel
        $this->_dataModel = new Vps_Model_FnF(array(
            'columns' => array('component_id', 'foo', 'bar'),
            'primaryKey' => 'component_id',
            'data' => array(
                array('component_id' => 1, 'foo'=>'xxy', 'bar' => 'aab'),
            )
        ));
    }

    public function testSaveRow()
    {
        $row = $this->_dataModel->getRow(1);

        $row->bar = 'foo';
        $row->save();
        Vps_Component_ModelObserver::getInstance()->process();
        $this->assertEquals(2, $this->_cacheModel->countActiveRows());

        $row->foo = 'foo';
        $row->save();
        Vps_Component_ModelObserver::getInstance()->process();
        $this->assertEquals(1, $this->_cacheModel->countActiveRows());
    }

    public function testDeleteRow()
    {
        $row = $this->_dataModel->getRow(1);

        $row->delete();
        Vps_Component_ModelObserver::getInstance()->process();
        $this->assertEquals(1, $this->_cacheModel->countActiveRows());
   }

    public function testInsertRow()
    {
        $row = $this->_dataModel->createRow(array(
            'component_id' => 2
        ));
        $row->save();
        Vps_Component_ModelObserver::getInstance()->process();
        $this->assertEquals(2, $this->_cacheModel->countActiveRows());
   }

    public function testInsertRow2()
    {
        $row = $this->_dataModel->createRow(array(
            'component_id' => 2, 'foo' => 'bar'
        ));
        $row->save();
        Vps_Component_ModelObserver::getInstance()->process();
        $this->assertEquals(1, $this->_cacheModel->countActiveRows());
   }

}