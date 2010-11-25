<?php
/**
 * @group Service
 */
class Vps_Srpc_ClientTest extends Vps_Test_TestCase
{
    private $_client;

    public function setUp()
    {
        parent::setUp();
        $this->_client = $this->getMock('Vps_Srpc_Client', array('_performRequest'));
    }

    public function testRequestWithParams()
    {
        $expectedArray = array(
            'method' => 'getMyId',
            'arguments' => serialize(array(1, 'foo')),
            'extraParams' => serialize(array())
        );
        $this->_client->expects($this->once())
            ->method('_performRequest')
            ->with($this->equalTo($expectedArray))
            ->will($this->returnValue(serialize(array('id' => 'foo'))));

        $this->assertEquals(array('id' => 'foo'), $this->_client->getMyId(1, 'foo'));
    }

    public function testRequestWithoutParams()
    {
        $expectedArray = array(
            'method' => 'syncIt',
            'arguments' => serialize(array()),
            'extraParams' => serialize(array())
        );
        $this->_client->expects($this->once())
            ->method('_performRequest')
            ->with($this->equalTo($expectedArray))
            ->will($this->returnValue(serialize('bar')));
        $this->assertEquals('bar', $this->_client->syncIt());
    }
}
