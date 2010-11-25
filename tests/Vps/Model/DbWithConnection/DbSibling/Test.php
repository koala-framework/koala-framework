<?php
/**
 * @group Model
 * @group Model_Db_Sibling
 * @group Model_Db
 * @group Model_DbWithConnection
 */
class Vps_Model_DbWithConnection_DbSibling_Test extends Vps_Test_TestCase
{
    public function setUp()
    {
        $this->_model = new Vps_Model_DbWithConnection_DbSibling_MasterModel();
    }
    public function tearDown()
    {
        $this->_model->dropTable();
    }

    public function testJoinWithWhereAndOrder()
    {
        $m = $this->_model;
        $m->createRow(array('foo' => 'a1', 'bar' => '0', 'baz' => 'admin'))->save();
        $m->createRow(array('foo' => 'b1', 'bar' => '0', 'baz' => 'admin'))->save();
        $m->createRow(array('foo' => 'c1', 'bar' => '0', 'baz' => 'admin'))->save();
        $m->createRow(array('foo' => 'd1', 'bar' => '0', 'baz' => 'management'))->save();
        $m->createRow(array('foo' => 'e1', 'bar' => '0', 'baz' => 'management'))->save();
        $m->createRow(array('foo' => 'f1', 'bar' => '0', 'baz' => 'todo'))->save();
        $m->createRow(array('foo' => 'g1', 'bar' => '0', 'baz' => 'management'))->save();

        $r = $m->getRows($m->select()
            ->whereEquals('bar', '0')
            ->whereEquals('baz', array('admin', 'management', 'todo'))
        );
        $this->assertEquals(7, count($r));

        $r = $m->getRows($m->select()
            ->whereEquals('bar', '0')
            ->whereEquals('baz', array('admin', 'management', 'todo'))
            ->order('baz', 'ASC')
        );
        $this->assertEquals(7, count($r));
    }

    public function testIt()
    {
        $m = $this->_model;

        $r = $m->getRow(1);
        $this->assertEquals('aaabbbccc', $r->foo);
        $this->assertEquals('abcd', $r->bar);
        $this->assertEquals('aha', $r->baz);

        $r = $m->getRow(2);
        $this->assertEquals('bam', $r->foo);
        $this->assertEquals('bum', $r->bar);
        $this->assertEquals(null, $r->baz);

        $r = $m->getRow($m->select()->whereEquals('baz', 'aha'));
        $this->assertNotNull($r);
        $this->assertEquals(1, $r->id);

        $r = $m->getRow($m->select()->whereNull('baz'));
        $this->assertNotNull($r);
        $this->assertEquals(2, $r->id);

        $r = $m->getRows($m->select()->order('baz'));
        $this->assertEquals(2, count($r));

        $r = $m->createRow();
        $r->foo = 'xxy';
        $r->baz = 'xxz';
        $r->save();
        $m->clearRows();
        $r = $m->getRow(3);
        $this->assertEquals('xxy', $r->foo);
        $this->assertEquals(null, $r->bar);
        $this->assertEquals('xxz', $r->baz);

        $r = $m->createRow();
        $r->foo = 'toDelete';
        $r->save();
        $select = $m->select()->whereEquals('foo', 'toDelete');
        $this->assertEquals(1, $m->countRows($select));
        $m->deleteRows($select);
        $this->assertEquals(0, $m->countRows($select));
    }

    public function testDuplicate()
    {
        $m = $this->_model;

        $r = $m->getRow(1)->duplicate();
        $this->assertEquals('aaabbbccc', $r->foo);
        $this->assertEquals('abcd', $r->bar);
        $this->assertEquals('aha', $r->baz);
    }
}
