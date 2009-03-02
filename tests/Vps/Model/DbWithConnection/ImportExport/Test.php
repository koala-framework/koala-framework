<?php
/**
 * @group Model_Db
 * @group Model_Db_Import_Export
 */
class Vps_Model_DbWithConnection_ImportExport_Test extends PHPUnit_Framework_TestCase
{
    public function testFormatSql()
    {
        $ex = new Vps_Model_DbWithConnection_DbSibling_ExportModel();
        $data = $ex->export(Vps_Model_Interface::FORMAT_SQL);

        // zweimal das export hernehmen, da die tabelle ja gleich heißen muss

        $r = $ex->getRow(1);
        $this->assertEquals(1, $r->id);

        $ex->deleteRows(array());

        $r = $ex->getRow(1);
        $this->assertEquals(null, $r);

        $ex->import(Vps_Model_Interface::FORMAT_SQL, $data);
        $r = $ex->getRow(1);

        $this->assertEquals(1, $r->id);
        $this->assertEquals('aaabbbccc', $r->foo);
        $this->assertEquals('abcd', $r->bar);
        $r = $ex->getRow(2);
        $this->assertEquals(2, $r->id);
        $this->assertEquals('bam', $r->foo);
        $this->assertEquals('bum', $r->bar);
    }

    public function testFormatArray()
    {
        $ex = new Vps_Model_DbWithConnection_DbSibling_ExportModel();
        $data = $ex->export(Vps_Model_Interface::FORMAT_ARRAY, new Vps_Model_Select());

        $check = array(
            array('id' => 1, 'foo' => 'aaabbbccc', 'bar' => 'abcd'),
            array('id' => 2, 'foo' => 'bam', 'bar' => 'bum')
        );
        $this->assertEquals($check, $data);

        $im = new Vps_Model_DbWithConnection_DbSibling_ImportModel();
        $r = $im->getRow(1);
        $this->assertEquals(null, $r);

        $im->import(Vps_Model_Interface::FORMAT_ARRAY, $data);
        $r = $im->getRow(1);
        $this->assertEquals(1, $r->id);
        $this->assertEquals('aaabbbccc', $r->foo);
        $this->assertEquals('abcd', $r->bar);
        $r = $im->getRow(2);
        $this->assertEquals(2, $r->id);
        $this->assertEquals('bam', $r->foo);
        $this->assertEquals('bum', $r->bar);
    }

}
