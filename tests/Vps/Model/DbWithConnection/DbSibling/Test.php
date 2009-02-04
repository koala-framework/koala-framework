<?php
/**
 * @group Model_Db_Sibling
 */
class Vps_Model_DbWithConnection_DbSibling_Test extends PHPUnit_Framework_TestCase
{
    public function testIt()
    {
        $m = new Vps_Model_DbWithConnection_DbSibling_MasterModel();

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
    }
}
