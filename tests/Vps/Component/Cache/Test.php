<?php
/**
 * @group Component_Cache
 */
class Vps_Component_Cache_Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Vps_Component_Cache::setBackend(Vps_Component_Cache::CACHE_BACKEND_FNF);
    }

    public function testDeleteByRow()
    {
        $cache = Vps_Component_Cache::getInstance();
        $cacheModel = $cache->getModel('cache');

        // Cache-Row einfügen
        $cacheModel->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('component_id' => '1'),
            array('component_id' => '2'),
            array('component_id' => '3'),
        ));

        // Meta-Row einfügen
        $cache->getModel('metaRow')->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array(
                'model' => 'Vps_Model_FnF',
                'field' => 'id',
                'value' => 1,
                'component_id' => 1
            ),
            array(
                'model' => 'Vps_Model_FnF',
                'field' => 'foo',
                'value' => 'yyz',
                'component_id' => 2
            ),
            array(
                'model' => 'Vps_Foo_Model',
                'field' => 'id',
                'value' => 1,
                'component_id' => 3
            ),
        ));

        // Datenmodel
        $model = new Vps_Model_FnF(array(
            'columns' => array('id', 'foo'),
            'primaryKey' => 'id',
            'data' => array(
                array('id' => 1, 'foo'=>'xxy'),
                array('id' => 2, 'foo'=>'yyz'),
                array('id' => 3, 'foo'=>'aab')
            )
        ));

        $cache->cleanByRow($model->getRow(3));
        $this->assertEquals(3, $cacheModel->countRows($cacheModel->select()->whereEquals('deleted', 0)));

        $cache->cleanByRow($model->getRow(1));
        $this->assertEquals(2, $cacheModel->countRows($cacheModel->select()->whereEquals('deleted', 0)));

        $cache->cleanByRow($model->getRow(2));
        $this->assertEquals(1, $cacheModel->countRows($cacheModel->select()->whereEquals('deleted', 0)));
    }

    public function testDeleteByRowWithComponent()
    {
        $cache = Vps_Component_Cache::getInstance();
        $cacheModel = $cache->getModel('cache');

        // Cache-Row einfügen
        $cacheModel->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('component_id' => 1),
            array('component_id' => 2),
            array('component_id' => 3)
        ));

        // Meta-Row einfügen
        $cache->getModel('metaRow')->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('model' => 'Vps_Model_FnF', 'value' => 1, 'component_id' => 1)
        ));

        // Component-Meta-Row einfügen
        $cache->getModel('metaComponent')->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('component_id' => 2, 'source_component_id' => 1), // wenn 1 gelöscht wird, wird auch 2 gelöscht
            array('component_id' => 3, 'source_component_id' => 2),
            array('component_id' => 3, 'source_component_id' => 1) // wegen Rekursion
        ));

        // Datenmodel
        $model = new Vps_Model_FnF(array('data' => array(array('id' => 1))));

        $cache->cleanByRow($model->getRow(1));
        $this->assertEquals(0, $cacheModel->countRows($cacheModel->select()->whereEquals('deleted', 0)));
    }

    public function testDeleteByModel()
    {
        $cache = Vps_Component_Cache::getInstance();
        $cacheModel = $cache->getModel('cache');

        // Cache-Row einfügen
        $cacheModel->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('component_class' => 'Vpc_Foo'),
            array('component_class' => 'Vpc_Bar'),
            array('component_class' => 'Vpc_FooBar')
        ));

        // Meta-Row einfügen
        $cache->getModel('metaModel')->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('model' => 'Vps_Model_FnF', 'component_class' => 'Vpc_Foo'),
            array('model' => 'Vps_Foo_Model', 'component_class' => 'Vpc_Bar')
        ));

        // Component-Meta-Row einfügen
        $cache->getModel('metaComponent')->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('component_class' => 'Vpc_Bar', 'source_component_class' => 'Vpc_Foo')
        ));

        $cache->cleanByModel(new Vps_Model_FnF());
        $this->assertEquals(1, $cacheModel->countRows($cacheModel->select()->whereEquals('deleted', 0)));
    }

    public function testDeleteCallback()
    {
        $cache = Vps_Component_Cache::getInstance();
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Cache_Root');

        // Callback-Meta-Row einfügen
        $cache->getModel('metaCallback')->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('model' => 'Vps_Model_FnF', 'field' => 'id', 'value' => 1, 'component_id' => 'root')
        ));

        // Datenmodel
        $model = new Vps_Model_FnF(array('data' => array(array('id' => 1))));
        $row = $model->getRow(1);

        $cache->cleanByCallback($row);

        $root = Vps_Component_Data_Root::getInstance();
        $callbacks = $root->getComponent()->getCallbacks();
        $this->assertEquals(1, count($callbacks));
        $this->assertEquals($row->toArray(), $callbacks[0]->toArray());
    }

    public function testDeleteWithObserver()
    {
        $cache = Vps_Component_Cache::getInstance();
        $cacheModel = $cache->getModel('cache');
        $cacheModel->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('component_id' => 1),
            array('component_id' => 2)
        ));
        $cache->getModel('metaRow')->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('model' => 'Vps_Model_FnF', 'value' => 1, 'component_id' => 1)
        ));
        $model = new Vps_Model_FnF(array('data' => array(array('id' => 1))));

        $model->getRow(1)->save();
        Vps_Component_ModelObserver::getInstance()->setSkipFnf(false);
        Vps_Component_ModelObserver::getInstance()->process();
        $this->assertEquals(1, $cacheModel->countRows($cacheModel->select()->whereEquals('deleted', 0)));
    }
}