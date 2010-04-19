<?php
/**
 * @group Model
 * @group Model_Db_Sibling_Proxy
 * @group Model_Db
 * @group Model_DbWithConnection
 */
class Vps_Model_DbWithConnection_DbSiblingProxy_Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_model = new Vps_Model_DbWithConnection_DbSiblingProxy_ProxyModel();
    }

    public function tearDown()
    {
        $this->_model->dropTable();
        Vps_Model_Abstract::clearInstances();
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

        $tableName = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_DbSiblingProxy_DbModel')
                        ->getTable()->info(Zend_Db_Table_Abstract::NAME);
        $m = new Vps_Model_Db(array('table'=>$tableName));
        $r = $m->getRow(3);
        $this->assertEquals('xxy', $r->foo);
        $this->assertEquals(null, $r->bar);

        $tableName = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_DbSiblingProxy_SiblingModel')
                        ->getTable()->info(Zend_Db_Table_Abstract::NAME);
        $m = new Vps_Model_Db(array('table'=>$tableName));
        $r = $m->getRow(3);
        $this->assertEquals('xxz', $r->baz);

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
