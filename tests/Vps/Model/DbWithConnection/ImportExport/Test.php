<?php
/**
 * @group Model
 * @group Model_Db
 * @group Model_DbWithConnection
 * @group Model_Db_Import_Export
 * @group slow
 */
class Vps_Model_DbWithConnection_ImportExport_Test extends PHPUnit_Framework_TestCase
{
    private $_model;
    private $_tableName;

    public function setUp()
    {
        $this->_tableName = 'dbexport'.uniqid();

        $this->_model = new Vps_Model_DbWithConnection_ImportExport_Model(array(
            "table" => $this->_tableName
        ));
        $this->_model->writeInitRows();
    }

    public function tearDown()
    {
        $this->_model->dropTable();
    }

    public function testServiceFormatSql()
    {
        $d = Zend_Registry::get('testDomain');
        $client = new Vps_Srpc_Client(array(
            'serverUrl' => "http://$d/vps/test/vps_model_db-with-connection_import-export_test/export",
            'extraParams' => array('table' => $this->_tableName)
        ));
        $model = new Vps_Model_Service(array('client' => $client));

        $r = $model->getRow(1);
        $this->assertEquals(1, $r->id);
        $this->assertEquals('aaabbbccc', $r->foo);

        $data = $model->export(Vps_Model_Interface::FORMAT_SQL);

        $model->deleteRows(array());
        $r = $model->getRow(1);
        $this->assertEquals(null, $r);

        $model->import(Vps_Model_Interface::FORMAT_SQL, $data);

        $r = $model->getRow(1);
        $this->assertEquals(1, $r->id);
        $this->assertEquals('aaabbbccc', $r->foo);
        $this->assertEquals('abcd', $r->bar);
        $r = $model->getRow(2);
        $this->assertEquals(2, $r->id);
        $this->assertEquals('bam', $r->foo);
        $this->assertEquals('bum', $r->bar);
    }

    public function testServiceFormatCsv()
    {
        $d = Zend_Registry::get('testDomain');
        $client = new Vps_Srpc_Client(array(
            'serverUrl' => "http://$d/vps/test/vps_model_db-with-connection_import-export_test/export",
            'extraParams' => array('table' => $this->_tableName)
        ));
        $model = new Vps_Model_Service(array('client' => $client));

        $r = $model->getRow(1);
        $this->assertEquals(1, $r->id);
        $this->assertEquals('aaabbbccc', $r->foo);

        $data = $model->export(Vps_Model_Interface::FORMAT_CSV);

        $model->deleteRows(array());
        $r = $model->getRow(1);
        $this->assertEquals(null, $r);

        $model->import(Vps_Model_Interface::FORMAT_CSV, $data);

        $r = $model->getRow(1);
        $this->assertEquals(1, $r->id);
        $this->assertEquals('aaabbbccc', $r->foo);
        $this->assertEquals('abcd', $r->bar);
        $r = $model->getRow(2);
        $this->assertEquals(2, $r->id);
        $this->assertEquals('bam', $r->foo);
        $this->assertEquals('bum', $r->bar);
        $r = $model->getRow(3);
        $this->assertEquals(3, $r->id);
        $this->assertEquals('bäm', $r->foo);
        $this->assertEquals('büm', $r->bar);
    }

    public function testFormatSql()
    {
        $ex = new Vps_Model_DbWithConnection_ImportExport_Model(array(
            "table" => $this->_tableName
        ));
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
        $r = $ex->getRow(3);
        $this->assertEquals(3, $r->id);
        $this->assertEquals('bäm', $r->foo);
        $this->assertEquals('büm', $r->bar);
    }

    public function testFormatCsv()
    {
        $ex = new Vps_Model_DbWithConnection_ImportExport_Model(array(
            "table" => $this->_tableName
        ));
        $data = $ex->export(Vps_Model_Interface::FORMAT_CSV);

        // zweimal das export hernehmen, da die tabelle ja gleich heißen muss

        $r = $ex->getRow(1);
        $this->assertEquals(1, $r->id);

        $ex->deleteRows(array());

        $r = $ex->getRow(1);
        $this->assertEquals(null, $r);

        $ex->import(Vps_Model_Interface::FORMAT_CSV, $data);

        $r = $ex->getRow(1);
        $this->assertEquals(1, $r->id);
        $this->assertEquals('aaabbbccc', $r->foo);
        $this->assertEquals('abcd', $r->bar);
        $r = $ex->getRow(2);
        $this->assertEquals(2, $r->id);
        $this->assertEquals('bam', $r->foo);
        $this->assertEquals('bum', $r->bar);
        $r = $ex->getRow(3);
        $this->assertEquals(3, $r->id);
        $this->assertEquals('bäm', $r->foo);
        $this->assertEquals('büm', $r->bar);
    }

    public function testFormatArray()
    {
        $ex = new Vps_Model_DbWithConnection_ImportExport_Model(array(
            "table" => $this->_tableName
        ));
        $data = $ex->export(Vps_Model_Interface::FORMAT_ARRAY, new Vps_Model_Select());

        $check = array(
            array('id' => 1, 'foo' => 'aaabbbccc', 'bar' => 'abcd'),
            array('id' => 2, 'foo' => 'bam', 'bar' => 'bum'),
            array('id' => 3, 'foo' => 'bäm', 'bar' => 'büm')
        );
        $this->assertEquals($check, $data);

        $im = new Vps_Model_DbWithConnection_ImportExport_Model(array(
            "table" => $this->_tableName
        ));
        $im->deleteRows(array());
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
        $r = $im->getRow(3);
        $this->assertEquals(3, $r->id);
        $this->assertEquals('bäm', $r->foo);
        $this->assertEquals('büm', $r->bar);
    }

    public function testFormatArrayBuffered()
    {
        $ex = new Vps_Model_DbWithConnection_ImportExport_Model(array(
            "table" => $this->_tableName
        ));
        $data = $ex->export(Vps_Model_Interface::FORMAT_ARRAY, new Vps_Model_Select());

        $im = new Vps_Model_DbWithConnection_ImportExport_Model(array(
            "table" => $this->_tableName
        ));
        $im->deleteRows(array());

        $im->import(Vps_Model_Interface::FORMAT_ARRAY, $data, array('buffer'=>true));
        $data = array(array('id'=>null, 'foo'=>'abcd', 'bar'=>'haha'));
        $im->import(Vps_Model_Interface::FORMAT_ARRAY, $data, array('buffer'=>true));
        $im->import(Vps_Model_Interface::FORMAT_ARRAY, $data, array('buffer'=>true));
        $im->writeBuffer();
        $r = $im->getRows();
        $this->assertEquals(5, count($r));
    }
}
