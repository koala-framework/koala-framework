<?php
class Kwf_Model_DbWithConnection_ArrayAccess_Test extends Kwf_Test_TestCase
{
    public function testOffsetGet()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ArrayAccess_Model');
        $m->setUp();
        $rows = $m->getRows();

        $row = $rows[0];
        $this->assertEquals($row->id, 1);
        $this->assertEquals($row->value, 'value1');

        $row = $rows[4];
        $this->assertEquals($row->id, 5);
        $this->assertEquals($row->value, 'value5');

        $row = $rows[2];
        $this->assertEquals($row->id, 3);
        $this->assertEquals($row->value, 'Peter Griffin');
        $m->dropTable();
    }
    public function testOffsetExists()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ArrayAccess_Model');
        $m->setUp();
        $rows = $m->getRows();

        $isRowSet = isset($rows[0]);
        $this->assertEquals($isRowSet, true);

        $isRowSet = isset($rows[4]);
        $this->assertEquals($isRowSet, true);

        $isRowSet = isset($rows[3]);
        $this->assertEquals($isRowSet, true);

        $isRowSet = isset($rows[-3]);
        $this->assertEquals($isRowSet, false);

        $isRowSet = isset($rows[5]);
        $this->assertEquals($isRowSet, false);
    }
}
