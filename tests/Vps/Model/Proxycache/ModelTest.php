<?php

/**
 * @group proxycache
 */
class Vps_Model_Proxycache_ModelTest extends PHPUnit_Framework_TestCase
{

    public function testCache()
    {
        $fnf1 = new Vps_Model_FnF(
        	array(
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

        $row = $proxyModel->createRow(array('id' => 1, 'en' =>'key', 'de' => 'index'));
        $row->save();

        $this->assertFalse($proxyModel->checkCache());

        $select = $proxyModel->select();
        $select->whereEquals('de', 'dings');
        $select->whereEquals('context', 'contexttest');
        $proxyModel->getRows($select);

        $this->assertTrue($proxyModel->checkCache());
    }

    public function testWhere()
    {
        $fnf1 = new Vps_Model_FnF(
        	array(
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
        $proxyModel->getRow(1);
        $this->assertTrue($proxyModel->checkCache());
        $row = $proxyModel->getRow(2);
        $this->assertEquals('dingsbums', $row->de);
        $this->assertEquals('foobar', $row->en);
    }

    public function testGetRowsMultipleInIndex()
    {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
        $fnf1 = new Vps_Model_FnF(
        	array(
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
        $this->assertEquals(array(
                array('id' => 1, 'en' => 'foo', 'context' => 'contexttest', 'de' => 'dings'),
                array('id' => 2, 'en' => 'foo', 'context' => 'contexttest', 'de' => 'dingsbums')
                ), $rows->toArray());
    }
    public function testDefaultValues()
    {
        $fnf = new Vps_Model_FnF(array(
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
        $fnf = new Vps_Model_FnF();
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

    /*
     * darf warten
     */
    public function testUniqueRowObjectNoPK()
    {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
        $fnf = new Vps_Model_FnF(array(
            'primaryKey' => null,
            'data' => array('name' => 'foo', 'de' => 'bar')
        ));
        $proxy = new Vps_Model_ProxyCache(array(
        	'proxyModel' => $fnf,
            'cacheSettings' => array(
                array('index' => array('name'),
            		  'columns' => array('de'))
            )
        ));

        $proxy->clearCache();
        $this->assertFalse($proxy->checkCache());

        $select = $proxy->select()->whereEquals('name', 'foo');
        $r1 = $proxy->getRow($select);
        $this->assertTrue($proxy->checkCache());
        $r2 = $proxy->getRow($select);
        $r3 = $proxy->getRow($select);
        $r4 = $proxy->getRow($proxy->select()->whereEquals('de', 'bar'));
        $r5 = $proxy->getRows($select)->current();
        $this->assertTrue($r1 === $r2);
        $this->assertTrue($r1 === $r3);
        $this->assertTrue($r1 === $r4);
        $this->assertTrue($r1 === $r5);
    }

    public function testUniqueRowObjectCreateRow()
    {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
        $fnf = new Vps_Model_FnF();
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
        $this->assertFalse($proxy->checkCache());
        $proxy->getRow(2);
        $this->assertTrue($proxy->checkCache());

        $r1 = $proxy->createRow(array('de' => 'whatever'));
        $r1->save();
        $this->assertFalse($proxy->checkCache());
        $r2 = $proxy->getRow(3);

        $this->assertTrue($r1 === $r2);
    }

    public function testUniqueRowObjectDeleteRow()
    {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
        $fnf = new Vps_Model_FnF();
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
        $proxy->getRow(2);
        $r1 = $proxy->getRow(3);
        $proxy->getRow(2)->delete();
        $this->assertFalse($proxy->checkCache());

        $r2 = $proxy->getRow(3);
        $this->assertTrue($r1 === $r2);
    }
}
