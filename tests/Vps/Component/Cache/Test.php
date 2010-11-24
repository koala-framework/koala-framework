<?php
/**
 * @group Cache
 */
class Vps_Component_Cache_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Root_Component');
    }

    public function testCacheId()
    {
        $cache = Vps_Component_Cache::getInstance();

        $cacheId = $cache->getCacheId('root');
        $this->assertEquals('root', $cacheId);

        $cacheId = $cache->getCacheId('root', Vps_Component_Cache::TYPE_MASTER);
        $this->assertEquals('root-master', $cacheId);

        $cacheId = $cache->getCacheId('root', Vps_Component_Cache::TYPE_HASCONTENT, 1);
        $this->assertEquals('root-hasContent1', $cacheId);

        $cacheId = $cache->getCacheId('root', Vps_Component_Cache::TYPE_PARTIAL, 2);
        $this->assertEquals('root~2', $cacheId);
    }

    public function testSave()
    {
        $cache = Vps_Component_Cache::getInstance();
        $cache->save('root', 'root', 'Vpc_Root');
        $cache->save('partial', 'root~2', 'Vpc_Root', 2);
        $cache->save('foobar', 'root', 'Vpc_Root');

        $rows = $cache->getModel()->getRows()->toArray();
        $this->assertEquals(count($rows), 2);
        $this->assertEquals($rows[1]['id'], 'root~2');
        $this->assertEquals($rows[1]['page_id'], 'root');
        $this->assertEquals($rows[1]['component_class'], 'Vpc_Root');
        $this->assertEquals($rows[1]['content'], 'partial');
        $this->assertTrue($rows[1]['expire'] > time());
        $this->assertEquals($rows[0]['content'], 'foobar');
    }

    public function testSaveMeta()
    {
        $cache = Vps_Component_Cache::getInstance();
        $cache->saveMeta('TestModel', null, 'a');
        $cache->saveMeta('TestModel', null, 'b', Vps_Component_Cache::META_CALLBACK);
        $cache->saveMeta('TestModel', null, 'c', Vps_Component_Cache::META_COMPONENT_CLASS);
        $cache->saveMeta('TestModel', 1, 'a');
        $cache->saveMeta('TestModel', 1, 'a');

        $rows = $cache->getMetaModel()->getRows()->toArray();
        $this->assertEquals(4, count($rows));
        $this->assertEquals('TestModel', $rows[0]['model']);
        $this->assertEquals('', $rows[0]['id']);
        $this->assertEquals('a', $rows[0]['value']);
        $this->assertEquals('b', $rows[1]['value']);
        $this->assertEquals('c', $rows[2]['value']);
        $this->assertEquals(Vps_Component_Cache::META_CACHE_ID, $rows[0]['type']);
        $this->assertEquals(Vps_Component_Cache::META_CALLBACK, $rows[1]['type']);
        $this->assertEquals(Vps_Component_Cache::META_COMPONENT_CLASS, $rows[2]['type']);
        $this->assertEquals(1, $rows[3]['id']);
    }

    public function testDelete()
    {
        $cache = Vps_Component_Cache::getInstance();
        $cache->save('', 'a', 'root', 'Vpc_Bar');
        $cache->save('', 'b', 'Vpc_Bar');
        $cache->save('', 'c1', 'Vpc_Foo');
        $cache->save('', 'c2', 'Vpc_Foo');

        $this->assertEquals(4, $cache->getModel()->countRows());
        $cache->clean(Vps_Component_Cache::CLEANING_MODE_ID, 'a');
        $this->assertEquals(3, $cache->getModel()->countRows());
        $cache->clean(Vps_Component_Cache::CLEANING_MODE_COMPONENT_CLASS, 'Vpc_Foo');
        $this->assertEquals(1, $cache->getModel()->countRows());
        $cache->clean(Vps_Component_Cache::CLEANING_MODE_ALL);
        $this->assertEquals(0, $cache->getModel()->countRows());
    }

    public function testDeleteByMeta()
    {
        $cache = Vps_Component_Cache::getInstance();
        $model = Vps_Model_Abstract::getInstance('Vps_Component_Cache_TestModel');
        $cache->saveMeta($model, null, 'a');
        $cache->saveMeta($model, 1, 'b');
        $cache->saveMeta($model, 2, 'Vpc_Foo', Vps_Component_Cache::META_COMPONENT_CLASS);
        $cache->save('', 'a', 'Vpc_Bar');
        $cache->save('', 'b', 'Vpc_Bar');
        $cache->save('', 'c1', 'Vpc_Foo');
        $cache->save('', 'c2', 'Vpc_Foo');

        $this->assertEquals(4, $cache->getModel()->countRows());
        $cache->clean(Vps_Component_Cache::CLEANING_MODE_META, $model->getRow(3));
        $this->assertEquals(3, $cache->getModel()->countRows());
        $cache->clean(Vps_Component_Cache::CLEANING_MODE_META, $model->getRow(2));
        $this->assertEquals(1, $cache->getModel()->countRows());
        $cache->clean(Vps_Component_Cache::CLEANING_MODE_META, $model->getRow(1));
        $this->assertEquals(0, $cache->getModel()->countRows());
    }

    public function testDeleteByMetaField()
    {
        $cache = Vps_Component_Cache::getInstance();
        $model = Vps_Model_Abstract::getInstance('Vps_Component_Cache_TestModelField');

        $cache->saveMeta($model, 7, 'a', Vps_Component_Cache::META_CACHE_ID, 'foo');
        $cache->saveMeta($model, null, 'b', Vps_Component_Cache::META_CACHE_ID, 'foo');
        $cache->save('', 'a', 'Vpc_Bar');
        $cache->save('', 'b', 'Vpc_Bar');

        $this->assertEquals(2, $cache->getModel()->countRows());
        $cache->clean(Vps_Component_Cache::CLEANING_MODE_META, $model->getRow(2));
        $this->assertEquals(1, $cache->getModel()->countRows());
        $cache->clean(Vps_Component_Cache::CLEANING_MODE_META, $model->getRow(1));
        $this->assertEquals(0, $cache->getModel()->countRows());
    }

    public function testDeleteWithObserver()
    {
        $cache = Vps_Component_Cache::getInstance();
        $cache->saveMeta('Vps_Component_Cache_TestModel', 1, 'a');
        $cache->saveMeta('Vps_Component_Cache_TestModel', null, 'b');
        $cache->save('', 'a', 'Vpc_Foo');
        $cache->save('', 'b', 'Vpc_Foo');

        $model = new Vps_Component_Cache_TestModel();

        $this->assertEquals(2, $cache->getModel()->countRows());

        $model->getRow(2)->save();
        Vps_Component_RowObserver::getInstance()->process();
        $this->assertEquals(1, $cache->getModel()->countRows());

        $model->getRow(1)->save();
        Vps_Component_RowObserver::getInstance()->process();
        $this->assertEquals(0, $cache->getModel()->countRows());
    }

    public function testDeleteCallback()
    {
        $model = new Vps_Component_Cache_TestModel();
        $row = $model->getRow(1);

        Vps_Component_Data_Root::setComponentClass('Vps_Component_Cache_Root');
        $root = Vps_Component_Data_Root::getInstance();

        $cache = Vps_Component_Cache::getInstance();
        $cache->saveMeta('Vps_Component_Cache_TestModel', null, 'root', Vps_Component_Cache::META_CALLBACK);

        $row->save();
        Vps_Component_RowObserver::getInstance()->process();

        $callbacks = $root->getComponent()->getCallbacks();
        $this->assertEquals(1, count($callbacks));
        $this->assertEquals($row->toArray(), $callbacks[0]->toArray());
    }

    public function testPreload()
    {
        $cache = Vps_Component_Cache::getInstance();
        $cache->save('a', 'a', 'Vpc_Foo');
        $cache->save('b', 'b', 'Vpc_Foo');

        $cache->preload(array('a', 'r'));
        $this->assertTrue($cache->shouldBeLoaded('a'));
        $this->assertFalse($cache->shouldBeLoaded('b'));
        $this->assertTrue($cache->shouldBeLoaded('r'));
        $this->assertTrue($cache->isLoaded('a'));
        $this->assertFalse($cache->isLoaded('b'));
        $this->assertFalse($cache->isLoaded('r'));
    }

    public function testPreloadPage()
    {
        $cache = Vps_Component_Cache::getInstance();
        $cache->save('a', 'a', 'a');
        $cache->save('a-1', 'a-1', 'Vpc_Foo');
        $cache->save('a-2', 'a-2', 'Vpc_Foo');
        $cache->save('a_1', 'a_1', 'Vpc_Foo');

        $cache->preload(array('a-1'));
        $this->assertFalse($cache->isLoaded('a'));
        $this->assertTrue($cache->isLoaded('a-1'));
        $this->assertFalse($cache->isLoaded('a-2'));

        $cache->preload(array('a'));
        $this->assertTrue($cache->isLoaded('a'));
        $this->assertTrue($cache->isLoaded('a-1'));
        $this->assertTrue($cache->isLoaded('a-2'));
        $this->assertFalse($cache->isLoaded('a_1'));
    }

}