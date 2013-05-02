<?php
/**
 * @group Model
 * @group Model_Db
 * @group Model_DbWithConnection
 * @group Model_Db_Import_Export
 * @group slow
 */
class Kwf_Model_DbWithConnection_ImportExport_Test extends Kwf_Test_TestCase
{
    private $_model;
    private $_tableName;

    public function setUp()
    {
        parent::setUp();
        $this->_tableName = 'dbexport'.uniqid();

        $this->_model = new Kwf_Model_DbWithConnection_ImportExport_Model(array(
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
        $client = new Kwf_Srpc_Client(array(
            'serverUrl' => "http://$d/kwf/test/kwf_model_db-with-connection_import-export_test/export",
            'extraParams' => array('table' => $this->_tableName)
        ));
        $model = new Kwf_Model_Service(array('client' => $client));

        $r = $model->getRow(1);
        $this->assertEquals(1, $r->id);
        $this->assertEquals('aaabbbccc', $r->foo);

        $data = $model->export(Kwf_Model_Interface::FORMAT_SQL);

        $model->deleteRows(array());
        $r = $model->getRow(1);
        $this->assertEquals(null, $r);

        $model->import(Kwf_Model_Interface::FORMAT_SQL, $data);

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
        $client = new Kwf_Srpc_Client(array(
            'serverUrl' => "http://$d/kwf/test/kwf_model_db-with-connection_import-export_test/export",
            'extraParams' => array('table' => $this->_tableName)
        ));
        $model = new Kwf_Model_Service(array('client' => $client));

        $r = $model->getRow(1);
        $this->assertEquals(1, $r->id);
        $this->assertEquals('aaabbbccc', $r->foo);

        $data = $model->export(Kwf_Model_Interface::FORMAT_CSV);

        $model->deleteRows(array());
        $r = $model->getRow(1);
        $this->assertEquals(null, $r);

        $model->import(Kwf_Model_Interface::FORMAT_CSV, $data);

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
        $ex = new Kwf_Model_DbWithConnection_ImportExport_Model(array(
            "table" => $this->_tableName
        ));
        $data = $ex->export(Kwf_Model_Interface::FORMAT_SQL);

        // zweimal das export hernehmen, da die tabelle ja gleich heißen muss

        $r = $ex->getRow(1);
        $this->assertEquals(1, $r->id);

        $ex->deleteRows(array());

        $r = $ex->getRow(1);
        $this->assertEquals(null, $r);

        $ex->import(Kwf_Model_Interface::FORMAT_SQL, $data);
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
        $ex = new Kwf_Model_DbWithConnection_ImportExport_Model(array(
            "table" => $this->_tableName
        ));
        $data = $ex->export(Kwf_Model_Interface::FORMAT_CSV);

        // zweimal das export hernehmen, da die tabelle ja gleich heißen muss

        $r = $ex->getRow(1);
        $this->assertEquals(1, $r->id);

        $ex->deleteRows(array());

        $r = $ex->getRow(1);
        $this->assertEquals(null, $r);

        $ex->import(Kwf_Model_Interface::FORMAT_CSV, $data);

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
        $ex = new Kwf_Model_DbWithConnection_ImportExport_Model(array(
            "table" => $this->_tableName
        ));
        $data = $ex->export(Kwf_Model_Interface::FORMAT_ARRAY, new Kwf_Model_Select());

        $check = array(
            array('id' => 1, 'foo' => 'aaabbbccc', 'bar' => 'abcd'),
            array('id' => 2, 'foo' => 'bam', 'bar' => 'bum'),
            array('id' => 3, 'foo' => 'bäm', 'bar' => 'büm')
        );
        $this->assertEquals($check, $data);

        $im = new Kwf_Model_DbWithConnection_ImportExport_Model(array(
            "table" => $this->_tableName
        ));
        $im->deleteRows(array());
        $r = $im->getRow(1);
        $this->assertEquals(null, $r);

        $im->import(Kwf_Model_Interface::FORMAT_ARRAY, $data);
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
        $ex = new Kwf_Model_DbWithConnection_ImportExport_Model(array(
            "table" => $this->_tableName
        ));
        $data = $ex->export(Kwf_Model_Interface::FORMAT_ARRAY, new Kwf_Model_Select());

        $im = new Kwf_Model_DbWithConnection_ImportExport_Model(array(
            "table" => $this->_tableName
        ));
        $im->deleteRows(array());

        $im->import(Kwf_Model_Interface::FORMAT_ARRAY, $data, array('buffer'=>true));
        $data = array(array('id'=>null, 'foo'=>'abcd', 'bar'=>'haha'));
        $im->import(Kwf_Model_Interface::FORMAT_ARRAY, $data, array('buffer'=>true));
        $im->import(Kwf_Model_Interface::FORMAT_ARRAY, $data, array('buffer'=>true));
        $im->writeBuffer();
        $r = $im->getRows();
        $this->assertEquals(5, count($r));
    }
    
    public function testUnion()
    {
        $model = new Kwf_Model_DbWithConnection_ImportExport_Model(array(
            "table" => $this->_tableName
        ));

        $s1 = new Kwf_Model_Select();
        $s1->whereEquals('id', 1);

        $s2 = new Kwf_Model_Select();
        $s2->whereEquals('id', 2);
        $s1->union($s2);

        $this->assertEquals(count($model->export(Kwf_Model_Interface::FORMAT_ARRAY, $s1)), 2);
        $this->assertEquals(count($model->getRows($s1)), 2);
    }
}
