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
                'column' => 'id',
                'value' => 1,
                'component_id' => 1
            ),
            array(
                'model' => 'Vps_Model_FnF',
                'column' => 'foo',
                'value' => 'yyz',
                'component_id' => 2
            ),
            array(
                'model' => 'Vps_Foo_Model',
                'column' => 'id',
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
        $this->assertEquals(3, $cacheModel->countActiveRows());

        $cache->cleanByRow($model->getRow(1));
        $this->assertEquals(2, $cacheModel->countActiveRows());

        $cache->cleanByRow($model->getRow(2));
        $this->assertEquals(1, $cacheModel->countActiveRows());
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
            array('model' => 'Vps_Model_FnF', 'column' => 'id', 'value' => 1, 'component_id' => 1)
        ));

        // Component-Meta-Row einfügen
        $cache->getModel('metaComponent')->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('component_id' => 1, 'target_component_id' => 2), // wenn 1 gelöscht wird, wird auch 2 gelöscht
            array('component_id' => 2, 'target_component_id' => 3),
            array('component_id' => 1, 'target_component_id' => 3) // wegen Rekursion
        ));

        // Datenmodel
        $model = new Vps_Model_FnF(array('data' => array(array('id' => 1))));

        $cache->cleanByRow($model->getRow(1));
        $this->assertEquals(0, $cacheModel->countActiveRows());
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
            array('model' => 'Vps_Model_FnF', 'component_class' => 'Vpc_Bar'),
            array('model' => 'Vps_Model_FnF', 'component_class' => 'Vpc_FooBar', 'pattern' => '{id}'), //darf nicht gelöscht werden, da pattern nur bei row aktiv wird
            array('model' => 'Vps_Model_FnF', 'component_class' => 'Vpc_FooBar', 'callback' => 1) //darf nicht gelöscht werden
        ));

        $cache->cleanByModel(new Vps_Model_FnF());
        $this->assertEquals(1, $cacheModel->countActiveRows());
    }

    public function testDeleteByModelComponent()
    {
        $cache = Vps_Component_Cache::getInstance();
        $cacheModel = $cache->getModel('cache');

        // Cache-Row einfügen
        $cacheModel->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('component_class' => 'Vpc_Foo'),
            array('component_class' => 'Vpc_Bar')
        ));

        // Meta-Row einfügen
        $cache->getModel('metaModel')->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('model' => 'Vps_Model_FnF', 'component_class' => 'Vpc_Foo')
        ));

        // Component-Meta-Row einfügen
        $cache->getModel('metaComponent')->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('component_class' => 'Vpc_Foo', 'target_component_class' => 'Vpc_Bar')
        ));

        $cache->cleanByModel(new Vps_Model_FnF());
        $this->assertEquals(0, $cacheModel->countActiveRows());
    }

    public function testDeleteByModelPattern()
    {
        $cache = Vps_Component_Cache::getInstance();
        $cacheModel = $cache->getModel('cache');

        // Cache-Row einfügen
        $cacheModel->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('component_id' => 'a_1', 'component_class' => 'Vpc_Foo'),
            array('component_id' => 'a_2', 'component_class' => 'Vpc_Foo')
        ));

        // Meta-Row einfügen
        $cache->getModel('metaModel')->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('model' => 'Vps_Model_FnF', 'component_class' => 'Vpc_Foo', 'pattern' => '{component_id}_{id}')
        ));

        // Datenmodel
        $model = new Vps_Model_FnF(array(
            'data' => array(array('id' => 1, 'component_id' => 'a')),
            'columns' => array('id', 'component_id')
        ));

        $cache->cleanByRow($model->getRow(1));
        $this->assertEquals(1, $cacheModel->countActiveRows());
    }

    public function testDeleteByModelPatternWildcard()
    {
        $cache = Vps_Component_Cache::getInstance();
        $cacheModel = $cache->getModel('cache');

        // Cache-Row einfügen
        $cacheModel->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('component_id' => 'a_1', 'component_class' => 'Vpc_Foo'),
            array('component_id' => 'b_1', 'component_class' => 'Vpc_Foo'),
            array('component_id' => 'c_1', 'component_class' => 'Vpc_Bar'),
            array('component_id' => 'a_2', 'component_class' => 'Vpc_Foo')
        ));

        // Meta-Row einfügen
        $cache->getModel('metaModel')->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('model' => 'Vps_Model_FnF', 'component_class' => 'Vpc_Foo', 'pattern' => '%_{id}')
        ));

        // Datenmodel
        $model = new Vps_Model_FnF(array(
            'data' => array(array('id' => 1, 'component_id' => 'a')),
            'columns' => array('id', 'component_id')
        ));

        $cache->cleanByRow($model->getRow(1));
        $this->assertEquals(2, $cacheModel->countActiveRows());
    }

    public function testStaticCallback()
    {
        $cache = Vps_Component_Cache::getInstance();
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Cache_Root');

        // Meta-Row einfügen
        $cache->getModel('metaModel')->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('model' => 'Vps_Model_FnF', 'component_class' => 'Vpc_Foo', 'pattern' => '{id}', 'callback' => true)
        ));

        // Datenmodel
        $model = new Vps_Model_FnF(array('data' => array(array('id' => 1))));
        $row = $model->getRow(1);

        $cache->cleanByRow($row);

        $root = Vps_Component_Data_Root::getInstance();
        $callbacks = $root->getComponent()->getCallbacks();
        $this->assertEquals(1, count($callbacks));
        $this->assertEquals($row->toArray(), $callbacks[0]->toArray());
    }

    public function testDeleteWithObserver()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Root_Component');
        $cache = Vps_Component_Cache::getInstance();

        $cacheModel = $cache->getModel('cache');
        $cacheModel->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('component_id' => 1),
            array('component_id' => 2)
        ));

        $cache->getModel('metaRow')->import(Vps_Model_Abstract::FORMAT_ARRAY, array(
            array('model' => 'Vps_Model_FnF', 'column' => 'id', 'value' => 1, 'component_id' => 1)
        ));

        Vps_Component_ModelObserver::clearInstance();
        $model = new Vps_Model_FnF(array('data' => array(array('id' => 1))));
        $model->getRow(1)->save();
        Vps_Component_ModelObserver::getInstance()->setSkipFnf(false);
        Vps_Component_ModelObserver::getInstance()->process();

        $this->assertEquals(1, $cacheModel->countActiveRows());
    }
}