<?php
/**
 * @group Model
 * @group Model_RowCache
 */
class Kwf_Model_RowCache_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        apc_clear_cache('user');
        Kwf_Cache::factory('Core', 'Memcached', array(
            'lifetime'=>null,
            'automatic_cleaning_factor' => false,
            'automatic_serialization'=>true))->clean();
        Kwf_Cache_Simple::resetZendCache();
        parent::setUp();
        Kwf_Events_Dispatcher::addListeners('Kwf_Model_RowCache_RowCacheModel');
        Kwf_Model_Abstract::getInstance('Kwf_Model_RowCache_SourceModel')->setData(array(
            array('id' => 1, 'foo' => 1, 'bar'=>'x1'),
            array('id' => 2, 'foo' => 2, 'bar'=>'x2'),
            array('id' => 3, 'foo' => 3, 'bar'=>'x3'),
            array('id' => 4, 'foo' => 4, 'bar'=>'x4'),
        ));
    }

    private function _createModelInstance()
    {
        Kwf_Model_Abstract::clearInstances();
        return Kwf_Model_Abstract::getInstance('Kwf_Model_RowCache_RowCacheModel');
    }

    public function testRead()
    {
        $m = $this->_createModelInstance();
        $source = Kwf_Model_Abstract::getInstance('Kwf_Model_RowCache_SourceModel');
        $this->assertEquals(1, $m->getRow(1)->foo);
        $this->assertEquals(1, $source->called['getRows']);
        $this->assertEquals(1, $m->getRow(1)->foo);
        $this->assertEquals(1, $source->called['getRows']);
        $this->assertEquals('x1', $m->getRow(1)->bar);
        $this->assertEquals(1, $source->called['getRows']);

        $m = $this->_createModelInstance();
        $source = Kwf_Model_Abstract::getInstance('Kwf_Model_RowCache_SourceModel');
        $this->assertEquals(1, $m->getRow(1)->foo); //gecached, hier nicht laden
        $this->assertEquals(0, $source->called['getRows']);
        $this->assertEquals(1, $m->getRow(1)->foo);
        $this->assertEquals(0, $source->called['getRows']);
        $this->assertEquals('x1', $m->getRow(1)->bar); //feld nicht gecached, hier wird laden
        $this->assertEquals(1, $source->called['getRows']);
    }

    public function testSave()
    {
        $m = $this->_createModelInstance();
        $source = Kwf_Model_Abstract::getInstance('Kwf_Model_RowCache_SourceModel');
        $this->assertEquals(1, $m->getRow(1)->foo);
        $this->assertEquals(1, $source->called['getRows']);


        $m = $this->_createModelInstance();
        $source = Kwf_Model_Abstract::getInstance('Kwf_Model_RowCache_SourceModel');
        $r = $m->getRow(1);
        $this->assertEquals(0, $source->called['getRows']);
        $r->foo = 'asdf';
        $this->assertEquals(1, $source->called['getRows']);
        $r->save(); //muss cache löschen

        $m = $this->_createModelInstance();
        $source = Kwf_Model_Abstract::getInstance('Kwf_Model_RowCache_SourceModel');
        $r = $m->getRow(1);
        $this->assertEquals(1, $source->called['getRows']);
        $this->assertEquals('asdf', $r->foo); //nicht mehr gecached, da cache gelöscht wurde
    }
}
