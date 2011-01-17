<?php
/**
 * @group Model
 * @group Service
 * @group Model_Service
 */
class Vps_Model_Service_ModelTest extends Vps_Test_TestCase
{
    private $_client;

    public function setUp()
    {
        parent::setUp();
        $this->_client = $this->getMock('Vps_Srpc_Client',
            array('getColumns', 'getPrimaryKey', 'getRow', 'countRows', 'getRows'),
            array(array('serverUrl' => 'invalid'.uniqid())), '', true
        );
        parent::setUp();
    }

    public function testIsEqual()
    {
        $model = new Vps_Model_Service(array('client' => $this->_client));
        $modelEqual = new Vps_Model_Service(array('client' => $this->_client));
        $modelNotEqual = new Vps_Model_Service(array('serverUrl' => 'http://service.vps-projekte.vivid'));

        $this->assertEquals(true, $model->isEqual($modelEqual));
        $this->assertEquals(false, $model->isEqual($modelNotEqual));
    }

    public function testGetColumns()
    {
        $this->_client->expects($this->once())
            ->method('getColumns')
            ->will($this->returnValue(array('id', 'name')));

        $model = new Vps_Model_Service(array('client' => $this->_client));
        $cols = $model->getColumns();
        $this->assertEquals(array('id', 'name'), $cols);
    }

    public function testCountRows()
    {
        $this->_client->expects($this->once())
            ->method('countRows')
            ->with($this->equalTo(array('name LIKE ?', "%hans%")))
            ->will($this->returnValue(4));

        $model = new Vps_Model_Service(array('client' => $this->_client));

        $result = $model->countRows(array('name LIKE ?', "%hans%"));
        $this->assertEquals(4, $result);
    }

    public function testGetRows()
    {
        $this->_client->expects($this->any())
            ->method('getPrimaryKey')
            ->will($this->returnValue('id'));
        $this->_client->expects($this->any())
            ->method('getColumns')
            ->will($this->returnValue(array('id', 'name')));
        $this->_client->expects($this->once())
            ->method('getRows')
            ->with(
                $this->equalTo(array('name LIKE ?', "%hans%")),
                $this->equalTo('name'),
                $this->equalTo(10),
                $this->equalTo(30)
            )
            ->will($this->returnValue(array(
                array('id' => 13, 'name' => 'bahansl'),
                array('id' => 347, 'name' => 'hans'),
                array('id' => 4, 'name' => 'hansi'),
                array('id' => 8, 'name' => 'mahans')
            )));

        $model = new Vps_Model_Service(array('client' => $this->_client));

        $fetchRowset = $model->getRows(array('name LIKE ?', "%hans%"), 'name', 10, 30);
        $rowsArray = $fetchRowset->toArray();

        $this->assertEquals(13, $rowsArray[0]['id']);
        $this->assertEquals('bahansl', $rowsArray[0]['name']);

        $this->assertEquals(347, $rowsArray[1]['id']);
        $this->assertEquals('hans', $rowsArray[1]['name']);

        $this->assertEquals(4, $rowsArray[2]['id']);
        $this->assertEquals('hansi', $rowsArray[2]['name']);

        $this->assertEquals(8, $rowsArray[3]['id']);
        $this->assertEquals('mahans', $rowsArray[3]['name']);
    }

    public function testGetRow()
    {
        $this->_client->expects($this->any())
            ->method('getPrimaryKey')
            ->will($this->returnValue('id'));
        $this->_client->expects($this->once())
            ->method('getRows')
            ->will($this->returnValue(array(
                array('id' => 3, 'firstname' => 'Max', 'lastname' => 'Muster'),
                array('id' => 13, 'firstname' => 'Hans', 'lastname' => 'Hermann'),
                array('id' => 4, 'firstname' => 'Foo', 'lastname' => 'Bar')
            )));

        $model = new Vps_Model_Service(array('client' => $this->_client));

        $findRow = $model->getRow(3);
        $this->assertEquals(3, $findRow->id);
        $this->assertEquals('Max', $findRow->firstname);
        $this->assertEquals('Muster', $findRow->lastname);
    }

    public function testSelect()
    {
        $model = new Vps_Model_Service(array('client' => $this->_client));
        $this->assertEquals('Vps_Model_Select', get_class($model->select()));
    }

    public function testCreateRow()
    {
        $this->_client->expects($this->any())
            ->method('getPrimaryKey')
            ->will($this->returnValue('id'));
        $model = new Vps_Model_Service(array('client' => $this->_client));
        $newRow = $model->createRow(array('name' => 'foo', 'type' => 'bar'));
        $this->assertEquals('foo', $newRow->name);
        $this->assertEquals('bar', $newRow->type);
    }

    public function testGetPrimaryKey()
    {
        $this->_client->expects($this->once())
            ->method('getPrimaryKey')
            ->will($this->returnValue('id2'));

        $model = new Vps_Model_Service(array('client' => $this->_client));
        $pk = $model->getPrimaryKey();
        $this->assertEquals('id2', $pk);
    }

}
