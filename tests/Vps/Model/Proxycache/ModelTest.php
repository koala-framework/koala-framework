<?php
/**
 * @group Model
 * @group proxycache
 */
class Vps_Model_Proxycache_ModelTest extends Vps_Test_TestCase
{

    public function testCache()
    {
        $fnf1 = new Vps_Model_FnF(
            array(
                'uniqueIdentifier' => 'unique',
                'columns' => array('id', 'en', 'de', 'context'),
                'data' => array(
                array('id' => 1, 'en' => 'foo', 'context' => 'contexttest', 'de' => 'dings'),
                array('id' => 2, 'en' => 'foobar', 'context' => 'contexttest', 'de' => 'dingsbums'),
                )
         ));

        $proxyModel = new Vps_Model_ProxyCache(array('proxyModel' => $fnf1,
                                                    'cacheSettings' => array(
                                                        array(
                                                            'index' => array('en'),
                                                            'columns' => array('de')
                                                        ),
                                                        array(
                                                            'index' => array('de', 'context'),
                                                            'columns' => array('en')
                                                        ),

                                                 )
                                           ));

        $row = $proxyModel->createRow(array('en' =>'key', 'de' => 'index'));
        $row->save();

        //$this->assertTrue($proxyModel->checkCache());

        $select = $proxyModel->select();
        $select->whereEquals('de', 'dings');
        $select->whereEquals('context', 'contexttest');
        $proxyModel->getRows($select);
        $this->assertTrue($proxyModel->checkCache());
    }

    public function testMoreThanOneResult ()
    {
        $fnf1 = new Vps_Model_FnF(
            array(
                'uniqueIdentifier' => 'unique',
                'columns' => array('id', 'en', 'de', 'context'),
                'data' => array(
                array('id' => 1, 'en' => 'foo', 'context' => 'contexttest', 'de' => 'dings'),
                array('id' => 2, 'en' => 'foobar', 'context' => 'contexttest', 'de' => 'dingsbums'),
                array('id' => 3, 'en' => 'foobar', 'context' => 'contexttest', 'de' => 'anderes dingsbums'),

                )
         ));

        $proxyModel = new Vps_Model_ProxyCache(array('proxyModel' => $fnf1,
                                                    'cacheSettings' => array(
                                                        array(
                                                            'index' => array('en'),
                                                            'columns' => array('de')
                                                        ),
                                                        array(
                                                            'index' => array('de', 'context'),
                                                            'columns' => array('en')
                                                        ),

                                                 )
                                           ));

        $proxyModel->clearCache();
        $select = $proxyModel->select();
        $select->whereEquals('en', 'foobar');
        $rows = $proxyModel->getRows($select);
        $i = 0;
        foreach ($rows as $row) {
            if (!$i) {
                $this->assertEquals('dingsbums', $row->de);
                $i++;
            } else {
                $this->assertEquals('anderes dingsbums', $row->de);
            }
        }
        $this->assertEquals(2, $rows->count());

    }

    public function testWhere()
    {
        $fnf1 = new Vps_Model_FnF(
            array(
                'uniqueIdentifier' => 'unique',
                'columns' => array('id', 'en', 'de', 'context'),
                'data' => array(
                array('id' => 1, 'en' => 'foo', 'context' => 'contexttest', 'de' => 'dings'),
                array('id' => 2, 'en' => 'foobar', 'context' => 'contexttest', 'de' => 'dingsbums')
                )
         ));

        $proxyModel = new Vps_Model_ProxyCache(array('proxyModel' => $fnf1,
                                                    'cacheSettings' => array(
                                                        array(
                                                            'index' => array('en'),
                                                            'columns' => array('de')
                                                        ),
                                                        array(
                                                            'index' => array('de', 'context'),
                                                            'columns' => array('en')
                                                        ),

                                                 )
                                           ));

        $select = $proxyModel->select();
        $select->whereEquals('de', 'dings');
        $select->whereEquals('context', 'contexttest');
        $this->assertEquals ('foo', $proxyModel->getRows($select)->current()->en);


    }

    public function testGetRow()
    {
        $fnf1 = new Vps_Model_FnF(
            array(
                'uniqueIdentifier' => 'unique',
                'columns' => array('id', 'en', 'de', 'context'),
                'data' => array(
                array('id' => 1, 'en' => 'foo', 'context' => 'contexttest', 'de' => 'dings'),
                array('id' => 2, 'en' => 'foobar', 'context' => 'contexttest', 'de' => 'dingsbums')
                )
         ));

        $proxyModel = new Vps_Model_ProxyCache(array('proxyModel' => $fnf1,
                                                    'cacheSettings' => array(
                                                        array(
                                                            'index' => array('en'),
                                                            'columns' => array('de')
                                                        ),
                                                        array(
                                                            'index' => array('de', 'context'),
                                                            'columns' => array('en')
                                                        )
                                                 )
                                           ));

       $this->assertEquals ('foo', $proxyModel->getRow(1)->en);
    }

    public function testGetRowCachedGetColumnNotInCache()
    {
        $fnf1 = new Vps_Model_FnF(
            array(
                'uniqueIdentifier' => 'unique',
                'columns' => array('id', 'en', 'de', 'context'),
                'data' => array(
                array('id' => 1, 'en' => 'foo', 'context' => 'contexttest', 'de' => 'dings'),
                array('id' => 2, 'en' => 'foobar', 'context' => 'contexttest', 'de' => 'dingsbums')
                )
         ));

        $proxyModel = new Vps_Model_ProxyCache(array('proxyModel' => $fnf1,
                                                    'cacheSettings' => array(
                                                        array(
                                                            'index' => array('id'),
                                                            'columns' => array('de')
                                                        )
                                                 )
                                           ));
        $proxyModel->clearCache();
        $proxyModel->getRow(1);
        $this->assertTrue($proxyModel->checkCache());
        $row = $proxyModel->getRow(2);
        $this->assertEquals('dingsbums', $row->de);
        $this->assertEquals('foobar', $row->en);
    }

    public function testGetRowsMultipleInIndex()
    {
        $fnf1 = new Vps_Model_FnF(
            array(
                'uniqueIdentifier' => 'unique',
                'columns' => array('id', 'en', 'de', 'context'),
                'data' => array(
                array('id' => 1, 'en' => 'foo', 'context' => 'contexttest', 'de' => 'dings'),
                array('id' => 2, 'en' => 'foo', 'context' => 'contexttest', 'de' => 'dingsbums')
                )
         ));

        $proxyModel = new Vps_Model_ProxyCache(array('proxyModel' => $fnf1,
                                                    'cacheSettings' => array(
                                                        array(
                                                            'index' => array('en'),
                                                            'columns' => array('de')
                                                        )
                                                 )
                                           ));
        $proxyModel->clearCache();
        $rows = $proxyModel->getRows($proxyModel->select()->whereEquals('en', 'foo'));
        $this->assertEquals(2, count($rows));
        $test = array(
                array('id' => 1, 'en' => 'foo', 'context' => 'contexttest', 'de' => 'dings'),
                array('id' => 2, 'en' => 'foo', 'context' => 'contexttest', 'de' => 'dingsbums')

        );
        $this->assertEquals(array(
                array('id' => 1, 'en' => 'foo', 'context' => 'contexttest', 'de' => 'dings'),
                array('id' => 2, 'en' => 'foo', 'context' => 'contexttest', 'de' => 'dingsbums')
                ), $rows->toArray());
    }

    public function testDefaultValues()
    {
        $fnf = new Vps_Model_FnF(array(
            'uniqueIdentifier' => 'unique',
            'default' => array('foo'=>'defaultFoo')
        ));
        $proxyModel = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('id'),
                      'columns' => array('de'))
            )
        ));
        $row = $fnf->createRow();
        $this->assertEquals('defaultFoo', $row->foo);

        $row = $proxyModel->createRow();
        $this->assertEquals('defaultFoo', $row->foo);
    }

    public function testUniqueRowObject()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('id'),
                      'columns' => array('de'))
            )
        ));

        $proxy->clearCache();
        $this->assertFalse($proxy->checkCache());
        $r1 = $proxy->getRow(2);
        $this->assertTrue($proxy->checkCache());
        $r2 = $proxy->getRow(2);
        $r3 = $proxy->getRow(2);
        $this->assertTrue($r1 === $r2);
        $this->assertTrue($r1 === $r3);
    }

    public function testUniqueRowObjectCreateRow()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 2, 'de' => 'foo')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('id'),
                      'columns' => array('de'))
            )
        ));

        $proxy->clearCache();
        $temp = $proxy->checkCache();
        $this->assertFalse($temp);
        //$this->assertTrue($proxy->checkCache());

        $r1 = $proxy->createRow(array('de' => 'whatever'));
        $r1->save();
        $r2 = $proxy->getRow(3);
        $this->assertTrue($r1 === $r2);
    }

    public function testUniqueRowObjectDeleteRow()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo2'),
            array('id' => 3, 'name' => 'foo3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('id'),
                      'columns' => array('de'))
            )
        ));
        $proxy->getRow(2);
        $r1 = $proxy->getRow(3);
        $proxy->getRow(2)->delete();

        $r2 = $proxy->getRow(3);
        $this->assertTrue($r1 === $r2);
    }

    public function testUniqueRowObjectMultipleIndizies()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo2', 'check' => 'lalala'),
            array('id' => 3, 'name' => 'foo3', 'check' => 'dadada')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('name'),
                      'columns' => array('id')),
                array('index' => array('check'),
                      'columns' => array('name'))
                )
        ));


        $proxy->clearCache();
        $r1 = $proxy->getRow(3);
        $r3 = $proxy->createRow(array('name' => 'test', 'check' => 'hallo'));
        $r3->save();
        $proxy->getRow(2)->delete();
        $r2 = $proxy->getRow($proxy->select()->whereEquals('name', 'foo3'));
        $this->assertTrue($r1 === $r2);
    }

    public function testEmptyCacheColumns()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo2'),
            array('id' => 3, 'name' => 'foo3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('name'),
                      'columns' => array())
            )
        ));
        $row = $proxy->getRow($proxy->select()->whereEquals('name', 'foo3'));
        $this->assertEquals(3, $row->id);
    }

    public function testVariableCols()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 2, 'en' => 'answer', 'context' => 'testContext', 'en_plural' => 'answers', 'de' => 'antwort', 'de_plural' => 'antworten'),
            array('id' => 3, 'en' => 'dog', 'de' => 'hund')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('en', 'context'),
                      'columns' => array())
            )
        ));
        Vps_Debug::enable();
        $proxy->clearCache();
        $row = $proxy->getRow($proxy->select()->whereEquals('en', 'answer')->whereEquals('context', 'testContext'));
        $this->assertEquals(2, $row->id);
        $this->assertEquals('answers', $row->en_plural);
    }

    public function testUpdateRow()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo2'),
            array('id' => 3, 'name' => 'foo3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('name'),
                      'columns' => array())
            )
        ));

        $proxy->clearCache();
        $row = $proxy->getRow($proxy->select()->whereEquals('name', 'foo3'));
        $this->assertEquals(3, $row->id);
        $row->name = 'foo4';
        $row->save();

        $row = $proxy->getRow($proxy->select()->whereEquals('name', 'foo4'));
        $this->assertNotNull($row);
        $this->assertEquals(3, $row->id);
    }

    public function testDeleteRow()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo2'),
            array('id' => 3, 'name' => 'foo3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('name'),
                      'columns' => array())
            )
        ));
        $row = $proxy->getRow($proxy->select()->whereEquals('name', 'foo3'));
        $this->assertEquals(3, $row->id);
        $row->delete();

        $row = $proxy->getRow($proxy->select()->whereEquals('name', 'foo3'));
        $this->assertNull($row);
    }

    public function testInsertRow()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo2'),
            array('id' => 3, 'name' => 'foo3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('name'),
                      'columns' => array())
            )
        ));
        $row = $proxy->createRow();
        $row->name = 'foo4';
        $row->save();
        $row = $proxy->getRow($proxy->select()->whereEquals('name', 'foo4'));
        $this->assertNotNull($row);
        $this->assertEquals(4, $row->id);
    }


    public function testGetSimpleAndCache()
    {
        $fnf = new Vps_Model_Proxycache_TestFnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo2'),
            array('id' => 3, 'name' => 'foo3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('id'),
                      'columns' => array('name'))
            )
        ));

        $proxy->clearCache();
        $fnf->getRowsCalled = 0;
        $row = $proxy->getRow($proxy->select()->whereEquals('id', '2'));
        $this->assertNotNull($row);
        $this->assertEquals('foo2', $row->name);
        $this->assertEquals(1, $fnf->getRowsCalled);

        $row = $proxy->getRow($proxy->select()->whereEquals('id', '2'));
        $this->assertEquals(1, $fnf->getRowsCalled);

        $fnf = new Vps_Model_Proxycache_TestFnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo2'),
            array('id' => 3, 'name' => 'foo3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('id'),
                      'columns' => array('name'))
            )
        ));
        Vps_Debug::enable();
        $row = $proxy->getRow($proxy->select()->whereEquals('id', '2'));
        $this->assertEquals(0, $fnf->getRowsCalled);
        $this->assertEquals('foo2', $row->name);
        $this->assertEquals(0, $fnf->getRowsCalled);

        $this->assertEquals(null, $row->name1);
        $this->assertEquals(1, $fnf->getRowsCalled);
    }

    public function testGetSimpleAndCacheNotUnique()
    {
        $fnf = new Vps_Model_Proxycache_TestFnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo2'),
            array('id' => 3, 'name' => 'foo3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('id'),
                      'columns' => array('name'))
            )
        ));

        $proxy->clearCache();
        $fnf->getRowsCalled = 0;
        $row = $proxy->getRow($proxy->select()->whereEquals('id', '2'));
        $this->assertNotNull($row);
        $this->assertEquals('foo2', $row->name);
        $this->assertEquals(1, $fnf->getRowsCalled);

        $row = $proxy->getRow($proxy->select()->whereEquals('id', '2'));
        $this->assertEquals(1, $fnf->getRowsCalled);

        $fnf = new Vps_Model_Proxycache_TestFnF(array('uniqueIdentifier' => 'notunique'));
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo2'),
            array('id' => 3, 'name' => 'foo3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('id'),
                      'columns' => array('name'))
            )
        ));

        $proxy->clearCache();
        $fnf->getRowsCalled = 0;
        $row = $proxy->getRow($proxy->select()->whereEquals('id', '2'));
        $this->assertNotNull($row);
        $this->assertEquals('foo2', $row->name);
        $this->assertEquals(1, $fnf->getRowsCalled);

        $row = $proxy->getRow($proxy->select()->whereEquals('id', '2'));
        $this->assertEquals(1, $fnf->getRowsCalled);

    }

    public function testUniqueRowObjectUpdateRow()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo2'),
            array('id' => 3, 'name' => 'foo3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('id'),
                      'columns' => array('de'))
            )
        ));

        $proxy->clearCache();
        $this->assertFalse($proxy->checkCache());
        $r1 = $proxy->getRow(2);
        $r1->id = 6;
        $r1->save();

        $r2 = $proxy->getRow(6);
        $this->assertTrue($r1 === $r2);
    }

    public function testUniqueRowNotInIndex()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo2'),
            array('id' => 3, 'name' => 'foo3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('id'),
                      'columns' => array('de'))
            )
        ));

        $r1 = $proxy->getRow(3);
        $r2 = $proxy->getRow($proxy->select()->whereEquals('name', 'foo3'));
        $this->assertTrue($r1 === $r2);
    }

    public function testXmlFindContextNull ()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 1, 'en' => 'foo', 'de' => 'dings2'),
            array('id' => 2, 'en' => 'foo3', 'de' => 'dings3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('en', 'context'),
                      'columns' => array('de'))
            )
        ));
         Vps_Debug::enable();
        $proxy->clearCache();
        $row = $proxy->getRow($proxy->select()->whereEquals('en', 'foo')->whereNull('context'));
        $this->assertEquals('dings2', $row->de);

        $row = $proxy->getRow($proxy->select()->whereEquals('en', 'foo')->whereEquals('context', ''));
        $this->assertNull($row);
    }

    public function testAddNull ()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 1, 'en' => 'foo', 'de' => 'dings2'),
            array('id' => 2, 'en' => 'foo3', 'de' => 'dings3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('en', 'context'),
                      'columns' => array('de'))
            )
        ));
        $proxy->clearCache();
        $row = $proxy->createRow(array('en' => 'foobar', 'de' => 'dingsbums', 'context' => null));
        $row->save();

        $row = $proxy->getRow($proxy->select()->whereEquals('en', 'foobar')->whereNull('context'));
        $this->assertEquals('dingsbums', $row->de);

        $row = $proxy->getRow($proxy->select()->whereEquals('en', 'foobar')->whereEquals('context', ''));
        $this->assertNull($row);
    }

    public function testAddEmptyStringToXml ()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 1, 'en' => 'foo', 'de' => 'dings2'),
            array('id' => 2, 'en' => 'foo3', 'de' => 'dings3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('en', 'context'),
                      'columns' => array('de'))
            )
        ));
        $proxy->clearCache();
        $row = $proxy->getRow(1);
        $row1 = $proxy->createRow(array('en' => 'foobar', 'de' => 'dingsbums', 'context' => ''));
        $row1->save();

        $row = $proxy->getRow($proxy->select()->whereEquals('en', 'foobar')->whereEquals('context', ''));
        $this->assertEquals('dingsbums', $row->de);

        $row = $proxy->getRow($proxy->select()->whereEquals('en', 'foobar')->whereNull('context'));
        $this->assertNull($row);
    }

    public function testUpdateValueToNull ()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 1, 'en' => 'foo', 'de' => 'dings2'),
            array('id' => 2, 'en' => 'foo3', 'de' => 'dings3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('en', 'context'),
                      'columns' => array('de'))
            )
        ));
        $proxy->clearCache();
        $row1 = $proxy->createRow(array('en' => 'foobar', 'de' => 'dingsbums', 'context' => 'hallo'));
        $row1->save();

        $row1->context = null;
        $row1->save();

        $row = $proxy->getRow($proxy->select()->whereEquals('en', 'foobar')->whereNull('context'));
        $this->assertEquals('dingsbums', $row->de);

        $row = $proxy->getRow($proxy->select()->whereEquals('en', 'foobar')->whereEquals('context', ''));
        $this->assertNull($row);
    }

    public function testGetValueNotWhichIsNotExistsColumnsNotSet()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 1, 'en' => 'foo', 'de' => 'dings2'),
            array('id' => 2, 'en' => 'foo3', 'de' => 'dings3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('en', 'context'),
                      'columns' => array('de'))
            )
        ));
        $row = $proxy->getRow(1);
        $this->assertTrue($row->__isset('sp'));
        $this->assertNull($row->sp);

    }

    public function testGetValueNotWhichIsNotExistsColumns()
    {
        $this->setExpectedException('Vps_Exception');
        $fnf = new Vps_Model_FnF(array(
                'uniqueIdentifier' => 'unique',
                'columns' => array('id', 'en', 'de', 'context')
        ));
        $fnf->setData(array(
            array('id' => 1, 'en' => 'foo', 'de' => 'dings2'),
            array('id' => 2, 'en' => 'foo3', 'de' => 'dings3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('en', 'context'),
                      'columns' => array('de'))
            )
        ));
        $row = $proxy->getRow(1);
        $this->assertFalse($row->__isset('sp'));
        $this->assertNull($row->sp);
    }

    public function testSetValueNotWhichIsNotExistsColumnsNotSet()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 1, 'en' => 'foo', 'de' => 'dings2'),
            array('id' => 2, 'en' => 'foo3', 'de' => 'dings3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('en', 'context'),
                      'columns' => array('de'))
            )
        ));
        $row = $proxy->getRow(1);
        $row->sp = "hallo";
        $this->assertEquals("hallo", $row->sp);
    }

    public function testSetValueNotWhichIsNotExistsColumns()
    {
        $this->setExpectedException('Vps_Exception');
        $fnf = new Vps_Model_FnF(array(
                'columns' => array('id', 'en', 'de', 'context'),
                'uniqueIdentifier' => 'unique'
        ));
        $fnf->setData(array(
            array('id' => 1, 'en' => 'foo', 'de' => 'dings2'),
            array('id' => 2, 'en' => 'foo3', 'de' => 'dings3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('en', 'context'),
                      'columns' => array('de'))
            )
        ));
        $row = $proxy->getRow(1);
        $row->sp = "value";
    }

    public function testWrongWhere()
    {
        $fnf = new Vps_Model_FnF(array(
                'columns' => array('id', 'en', 'de', 'context'),
                'uniqueIdentifier' => 'unique'
        ));
        $fnf->setData(array(
            array('id' => 1, 'en' => 'foo', 'de' => 'dings2'),
            array('id' => 2, 'en' => 'foo3', 'de' => 'dings3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('en', 'context'),
                      'columns' => array('de'))
            )
        ));
        $row = $proxy->getRow($proxy->select()->whereEquals('x', 'foo')->whereNull('context'));
        $this->assertNull($row);
    }

    public function testUnderscoreAtEndOfEntry()
    {
        $fnf = new Vps_Model_FnF(array(
                'columns' => array('id', 'en', 'de'),
                'uniqueIdentifier' => 'unique'
        ));
        $fnf->setData(array(
            array('id' => 1, 'en' => 'foo_', 'de' => 'dings2'),
            array('id' => 2, 'en' => 'foo3', 'de' => 'dings3')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('en'),
                      'columns' => array('de'))
            )
        ));
        $row = $proxy->getRow($proxy->select()->whereEquals('en', 'foo_'));
        $this->assertNotNull($row);

    }

    public function testWhereNullTwoColumns()
    {
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 1, 'en' => 'foo', 'de' => 'dings2'),
            array('id' => 2, 'en' => 'foo3', 'de' => 'dings3'),
            array('id' => 3, 'en' => null, 'de' => null)
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('id', 'en', 'de'),
                      'columns' => array('de'))
            )
        ));
        Vps_Debug::enable();
        $proxy->clearCache();
        $row = $proxy->getRow($proxy->select()->whereEquals('id', 3)
                                            ->whereNull('en')
                                            ->whereNull('de'));
        $this->assertEquals(3, $row->id);
    }

    public function testSearchKeys()
    {
        Vps_Debug::enable();
        $fnf = new Vps_Model_FnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 1, 'en' => 'foo', 'de' => 'bar', 'sp' => 'blub'),
            array('id' => 2, 'en' => 'foo_', 'de' => 'bar', 'sp' => 'blub'),
            array('id' => 3, 'en' => 'foo', 'de' => '', 'sp' => 'blub'),
            array('id' => 4, 'en' => '', 'de' => '', 'sp' => ''),
            array('id' => 5, 'en' => null, 'de' => null, 'sp' => null),
            array('id' => 6, 'en' => 'foo', 'de' => 'bar', 'sp' => null),
            array('id' => 7, 'en' => '__', 'de' => '___', 'sp' => '____'),
            array('id' => 8, 'en' => null, 'de' => '___', 'sp' => '____'),
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('en', 'de', 'sp'),
                      'columns' => array())
            )
        ));

        $proxy->clearCache();

        $row = $proxy->getRow($proxy->select()->whereEquals('en', 'foo')->whereEquals('de', 'bar')
                                                ->whereEquals('sp', 'blub'));
        $this->assertNotNull($row);
        $this->assertEquals(1, $row->id);

        $row = $proxy->getRow($proxy->select()->whereEquals('en', 'foo_')->whereEquals('de', 'bar')
                                                ->whereEquals('sp', 'blub'));
        $this->assertNotNull($row);
        $this->assertEquals(2, $row->id);

        $row = $proxy->getRow($proxy->select()->whereEquals('en', 'foo')->whereEquals('de', '')
                                                ->whereEquals('sp', 'blub'));
        $this->assertNotNull($row);
        $this->assertEquals(3, $row->id);

        $row = $proxy->getRow($proxy->select()->whereEquals('en', '')->whereEquals('de', '')
                                                ->whereEquals('sp', ''));
        $this->assertNotNull($row);
        $this->assertEquals(4, $row->id);

        $row = $proxy->getRow($proxy->select()->whereNull('en')->whereNull('de')
                                                ->whereNull('sp'));
        $this->assertNotNull($row);
        $this->assertEquals(5, $row->id);

        $row = $proxy->getRow($proxy->select()->whereEquals('en', 'foo')->whereEquals('de', 'bar')
                                                ->whereNull('sp'));
        $this->assertNotNull($row);
        $this->assertEquals(6, $row->id);

        $row = $proxy->getRow($proxy->select()->whereEquals('sp', '____')->whereEquals('de', '___')
                                                ->whereEquals('en', '__'));
        $this->assertNotNull($row);
        $this->assertEquals(7, $row->id);

        $row = $proxy->getRow($proxy->select()->whereNull('en')->whereEquals('de', '___')->whereEquals('sp', '____'));
        $this->assertNotNull($row);
        $this->assertEquals(8, $row->id);
    }

    public function testNewlineInIndex()
    {
        $fnf = new Vps_Model_Proxycache_TestFnF(array('uniqueIdentifier' => 'unique'));
        $fnf->setData(array(
            array('id' => 1, 'en' => "foo\nbar", 'de' => "foo\\nbar"),
        ));
        $proxy = new Vps_Model_ProxyCache(array(
            'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('en'),
                      'columns' => array()),
                array('index' => array('de'),
                      'columns' => array())

            )
        ));
        Vps_Debug::enable();
        $proxy->clearCache();
        $row = $proxy->getRow($proxy->select()->whereEquals('en', "foo\nbar"));
        $this->assertEquals(1, $fnf->getRowsCalled);

        $row = $proxy->getRow($proxy->select()->whereEquals('en', "foo\nbar"));
        $this->assertEquals(1, $row->id);
        $this->assertEquals(1, $fnf->getRowsCalled);

        $row = $proxy->getRow($proxy->select()->whereEquals('de', "foo\\nbar"));
        $this->assertEquals(2, $fnf->getRowsCalled);

        $row = $proxy->getRow($proxy->select()->whereEquals('de', "foo\\nbar"));
        $this->assertEquals(1, $row->id);
        $this->assertEquals(2, $fnf->getRowsCalled);
    }
}
