<?php
/**
 * @group Service
 */
class Vps_Srpc_HandlerModelTest extends Vps_Test_TestCase
{
    private $_handler;
    private $_modelData = array(
        3 => array('id' => 3, 'firstname' => 'Hans', 'lastname' => 'Huber'),
        8 => array('id' => 8, 'firstname' => 'Foo', 'lastname' => 'Bar'),
        31 => array('id' => 31, 'firstname' => 'Max', 'lastname' => 'Muster'),
        641 => array('id' => 641, 'firstname' => 'Gerhart', 'lastname' => 'Mayer')
    );

    public function setUp()
    {
        parent::setUp();
        $model = new Vps_Model_FnF(array(
            'columns' => array('id', 'firstname', 'lastname'),
            'data' => array(
                $this->_modelData[3],
                $this->_modelData[8],
                $this->_modelData[31],
                $this->_modelData[641]
            )
        ));
        $this->_handler = new Vps_Srpc_Handler_Model(array('model' => $model));
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testNoModel()
    {
        $m = new Vps_Srpc_Handler_Model();
        $m->getModel();
    }

    public function testGetRow()
    {
        $row = $this->_handler->getRow(3);
        $this->assertEquals($this->_modelData[3], $row);

        $row = $this->_handler->getRow(31);
        $this->assertEquals($this->_modelData[31], $row);

        $row = $this->_handler->getRow(999);
        $this->assertEquals(null, $row);
    }

    public function testCountRows()
    {
        $this->assertEquals(4, $this->_handler->countRows());

        $select = $this->_handler->getModel()->select();
        $select->whereEquals('firstname', array('Foo', 'Max'));
        $this->assertEquals($this->_handler->countRows($select), 2);

        $model = new Vps_Model_FnF(array(
            'columns' => array('id', 'firstname', 'lastname'),
            'data' => array()
        ));
        $handler = new Vps_Srpc_Handler_Model(array('model' => $model));
        $this->assertEquals(0, $handler->countRows());
    }

    public function testGetRows()
    {
        $select = $this->_handler->getModel()->select();
        $select->whereEquals('id', array(3));

        $result = $this->_handler->getRows($select);
        $this->assertEquals(array($this->_modelData[3]), $result);

        $select->whereEquals('id', array(3, 8));
        $result = $this->_handler->getRows($select);
        $this->assertEquals(array($this->_modelData[3], $this->_modelData[8]), $result);

        $select->whereEquals('id', array(3, 8));
        $select->order('id', 'DESC');
        $result = $this->_handler->getRows($select);
        $this->assertEquals(array($this->_modelData[8], $this->_modelData[3]), $result);

        $result = $this->_handler->getRows();
        $this->assertEquals(array_values($this->_modelData), $result);
    }

    public function testGetColumns()
    {
        $result = $this->_handler->getColumns();
        $this->assertEquals(array('id', 'firstname', 'lastname'), $result);
    }

    public function testGetPrimaryKey()
    {
        $result = $this->_handler->getPrimaryKey();
        $this->assertEquals('id', $result);
    }

    public function testRowSave()
    {
        // save a new row
        $data = array('firstname' => 'Herbert');
        $result = $this->_handler->rowSave(null, $data);
        $this->assertEquals(array('id' => 642, 'firstname' => 'Herbert', 'lastname' => ''), $result);

        // save without data
        $data = array();
        $result = $this->_handler->rowSave(3, $data);
        $this->assertEquals(false, $result);

        // save an existing row
        $data = $this->_modelData[3];
        $data['id'] = 4;
        $data['firstname'] = 'Hf';
        $result = $this->_handler->rowSave(3, $data);
        $this->assertEquals(array('id' => 4, 'firstname' => 'Hf', 'lastname' => 'Huber'), $result);
    }

    public function testRowDelete()
    {
        $model = new Vps_Model_FnF(array(
            'columns' => array('id', 'firstname', 'lastname'),
            'data' => array(
                $this->_modelData[3],
                $this->_modelData[8],
                $this->_modelData[31],
                $this->_modelData[641]
            )
        ));
        $this->_handler = new Vps_Srpc_Handler_Model(array('model' => $model));

        // delete a row that does not exist
        $result = $this->_handler->rowDelete(1);
        $this->assertEquals(false, $result);

        // delete a row that exists
        $result = $this->_handler->rowDelete(641);
        $this->assertEquals(true, $result);

        // TODO: Gemocktes FnF Model das Gemockte Row zurückgibt um zu prüfen,
        // ob in row delete() aufgerufen wurde. gleiches gilt fürs save()
    }

}
