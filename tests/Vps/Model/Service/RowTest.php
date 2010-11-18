<?php
/**
 * @group Service
 * @group Model_Service
 */
class Vps_Model_Service_RowTest extends PHPUnit_Framework_TestCase
{
    private $_client;
    private $_multisaveRow;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass(false);

        $this->_client = $this->getMock('Vps_Srpc_Client',
            array('rowSave', 'rowDelete', 'getPrimaryKey', 'getColumns'),
            array(array('serverUrl' => 'invalid'.uniqid())), '', true
        );
        $this->_client->expects($this->any())
            ->method('getColumns')
            ->will($this->returnValue(array('id', 'firstname', 'lastname')));
        $this->_client->expects($this->any())
            ->method('getPrimaryKey')
            ->will($this->returnValue('id'));
    }

    public function testCreate()
    {
        $this->_client->expects($this->once())
            ->method('rowSave')
            ->with(null, $this->equalTo(array('id' => null, 'firstname' => 'Herbert', 'lastname' => 'Huber')))
            ->will($this->returnValue(array('id' => 34, 'firstname' => 'Herbert', 'lastname' => 'Huber')));

        $model = new Vps_Model_Service(array('client' => $this->_client));
        $row = $model->createRow();
        $row->firstname = 'Herbert';
        $row->lastname = 'Huber';
        $row->save();

        $this->assertEquals(34, $row->id);
        $this->assertEquals('Herbert', $row->firstname);
        $this->assertEquals('Huber', $row->lastname);
    }

    public function testCreateSetId()
    {
        $this->_client->expects($this->once())
            ->method('rowSave')
            ->with(null, $this->equalTo(array('id' => 29, 'firstname' => 'Herbert', 'lastname' => 'Huber')))
            ->will($this->returnValue(array('id' => 29, 'firstname' => 'Herbert', 'lastname' => 'Huber')));

        $model = new Vps_Model_Service(array('client' => $this->_client));
        $row = $model->createRow();
        $row->id = 29;
        $row->firstname = 'Herbert';
        $row->lastname = 'Huber';
        $row->save();

        $this->assertEquals(29, $row->id);
        $this->assertEquals('Herbert', $row->firstname);
        $this->assertEquals('Huber', $row->lastname);
    }

    public function testSaveChangeId()
    {
        $this->_client->expects($this->once())
            ->method('rowSave')
            ->with($this->equalTo(4), $this->equalTo(array('id' => 432, 'firstname' => 'moo', 'lastname' => 'Mayer')))
            ->will($this->returnValue(array('id' => 432, 'firstname' => 'moo', 'lastname' => 'Mayer')));

        $row = new Vps_Model_Service_Row(array(
            'model' => new Vps_Model_Service(array('client' => $this->_client)),
            'data' => array(
                'id' => 4,
                'firstname' => 'moo',
                'lastname' => 'boo'
            )
        ));

//         $this->assertEquals(4, $row->id);
        $row->id = 432;
        $row->lastname = 'Mayer';
//         $this->assertEquals(432, $row->id);
        $row->save();

//         $this->assertEquals(432, $row->id);
//         $this->assertEquals('moo', $row->firstname);
//         $this->assertEquals('Mayer', $row->lastname);
    }

    public function testSaveKeepId()
    {
        $this->_client->expects($this->once())
            ->method('rowSave')
            ->with($this->equalTo(4), $this->equalTo(array('id' => 4, 'firstname' => 'moo', 'lastname' => 'Mayer')))
            ->will($this->returnValue(array('id' => 4, 'firstname' => 'moo', 'lastname' => 'Mayer')));

        $row = new Vps_Model_Service_Row(array(
            'model' => new Vps_Model_Service(array('client' => $this->_client)),
            'data' => array(
                'id' => 4,
                'firstname' => 'moo',
                'lastname' => 'boo'
            )
        ));

        $row->lastname = 'Mayer';
        $row->save();

        $this->assertEquals(4, $row->id);
        $this->assertEquals('moo', $row->firstname);
        $this->assertEquals('Mayer', $row->lastname);
    }

    public function testDelete()
    {
        $this->_client->expects($this->once())
            ->method('rowDelete')
            ->with($this->equalTo(123));

        $row = new Vps_Model_Service_Row(array(
            'model' => new Vps_Model_Service(array('client' => $this->_client)),
            'data' => array(
                'id' => 123,
                'firstname' => 'moo',
                'lastname' => 'boo'
            )
        ));

        $row->delete();

        $this->assertEquals(null, $row->id);
        $this->assertEquals(null, $row->firstname);
        $this->assertEquals(null, $row->lastname);
    }
}
