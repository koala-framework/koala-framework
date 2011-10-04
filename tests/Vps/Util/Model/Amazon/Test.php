<?php
/**
 * @group Amazon
 * @group slow
 * alle sleeps damit der webservice nicht überfordert wird
 */
class Vps_Util_Model_Amazon_Test extends Vps_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        sleep(1);
        Vps_Model_Abstract::clearInstances();
    }

    public function testNodes()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_Amazon_Products');

        $select = $m->select();
        $select->whereEquals('Keywords', 'php');
        $select->whereEquals('SearchIndex', 'Books');
        $select->limit(3);

        $rows = $m->getRows($select);
        sleep(1);
        $this->assertEquals(3, count($rows));
        $nodes = array();
        foreach ($rows as $row) {
            foreach ($row->getChildRows('ProductsToNodes') as $r) {
                sleep(1);
                $node = $r->getParentRow('Node');
                $nodes[] = $node->name;
            }
            $this->assertContains('PHP', $row->title);
            $this->assertContains('PHP', $nodes);
        }
    }
    public function testPaging()
    {
        $m = new Vps_Util_Model_Amazon_Products;

        $select = $m->select();
        $select->whereEquals('Keywords', 'php');
        $select->whereEquals('SearchIndex', 'Books');
        $select->limit(10);
        $asins = array();
        foreach ($m->getRows($select) as $r) {
            $asins[] = $r->asin;
        }
        sleep(1);
        $select->limit(10, 10);
        foreach ($m->getRows($select) as $r) {
            $asins[] = $r->asin;
        }
        sleep(1);
        $select->limit(10, 20);
        foreach ($m->getRows($select) as $r) {
            $asins[] = $r->asin;
        }
        sleep(1);
        $this->assertEquals(count($asins), count(array_unique($asins)));
    }

    public function testPaging2()
    {
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();
        $m = new Vps_Util_Model_Amazon_Products;

        $select = $m->select();
        $select->whereEquals('Keywords', 'php');
        $select->whereEquals('SearchIndex', 'Books');
        $select->limit(1);
        $rows1 = $m->getRows($select);
        $this->assertEquals(1, $rows1->count());

        $select = $m->select();
        $select->whereEquals('Keywords', 'php');
        $select->whereEquals('SearchIndex', 'Books');
        $select->limit(1, 1);
        $rows2 = $m->getRows($select);
        $this->assertEquals(1, $rows2->count());

        $select = $m->select();
        $select->whereEquals('Keywords', 'php');
        $select->whereEquals('SearchIndex', 'Books');
        $rows3 = $m->getRows($select);
        $this->assertEquals(10, $rows3->count());
        foreach ($rows3 as $r) {
            $asins[] = $r->asin;
        }

        $this->assertEquals($asins[0], $rows1->current()->asin);
        $this->assertEquals($asins[1], $rows2->current()->asin);

        $this->assertEquals(1, Vps_Benchmark::getCounterValue('Service Amazon request'));
        sleep(1);
    }

    public function testGetRow()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_Amazon_Products');
        $this->assertNotNull($m->getRow('3772369197'));
        $this->assertNotNull($m->getRows($m->select()->whereId('3772369197')));
        $this->assertNotNull($m->getRows($m->select()->whereEquals('asin', '3772369197')));
    }

    public function testInvalidLimit()
    {
        $this->setExpectedException("Vps_Exception");
        $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_Amazon_Products');
        $select = $m->select();
        $select->whereEquals('Keywords', 'php');
        $select->whereEquals('SearchIndex', 'Books');
        $select->limit(11);
        $m->getRows($select);
    }
    public function testMultipleOrder()
    {
        $this->setExpectedException("Vps_Exception");
        $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_Amazon_Products');
        $select = $m->select();
        $select->whereEquals('Keywords', 'php');
        $select->whereEquals('SearchIndex', 'Books');
        $select->limit(10);
        $select->order('foo');
        $select->order('bar');
        $m->getRows($select);
    }
    public function testOrderDesc()
    {
        $this->setExpectedException("Vps_Exception");
        $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_Amazon_Products');
        $select = $m->select();
        $select->whereEquals('Keywords', 'php');
        $select->whereEquals('SearchIndex', 'Books');
        $select->limit(10);
        $select->order('foo', 'DESC');
        $m->getRows($select);
    }

    public function testPerformance()
    {
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();
        $m = new Vps_Util_Model_Amazon_Products();

        $select = $m->select();
        $select->whereEquals('Keywords', 'php');
        $select->whereEquals('SearchIndex', 'Books');
        $select->limit(10);
        $m->getRows($select);

        $this->assertEquals(1, Vps_Benchmark::getCounterValue('Service Amazon request'));

        $m->countRows($select);
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('Service Amazon request'));

        $select->limit(10, 10);
        $m->countRows($select);
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('Service Amazon request'));
        sleep(1);

        Vps_Benchmark::reset();
        $m = new Vps_Util_Model_Amazon_Products();
        $select->limit(10, 10);
        $m->getRows($select);
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('Service Amazon request'));

        $select->limit(10, 0);
        $m->countRows($select);
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('Service Amazon request'));

        Vps_Benchmark::disable();
    }
}
