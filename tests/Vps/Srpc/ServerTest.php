<?php
/**
 * @group Service
 */
class Vps_Srpc_ServerTest extends PHPUnit_Framework_TestCase
{
    private $_client;

    public function setUp()
    {
        $this->_client = $this->getMock('Vps_Srpc_Client', array('_performRequest'));
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testNoClassSetted()
    {
        $srv = new Vps_Srpc_Server();
        $srv->handle();
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testNoMethodTransmitted()
    {
        $srv = new Vps_Srpc_Server(array('handler' => 'Vps_Srpc_TestClasses_Handler'));
        $srv->handle(null, array(3));
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testNoArgumentsTransmitted()
    {
        $srv = new Vps_Srpc_Server(array('handler' => 'Vps_Srpc_TestClasses_Handler'));
        $srv->handle('getRow');
    }

    public function testSetClass()
    {
        $compare = new Vps_Srpc_TestClasses_Handler();

        $srv = new Vps_Srpc_Server(array('handler' => new Vps_Srpc_TestClasses_Handler()));
        $this->assertEquals($srv->getHandler(), $compare);

        $srv = new Vps_Srpc_Server(array('handler' => 'Vps_Srpc_TestClasses_Handler'));
        $this->assertEquals($srv->getHandler(), $compare);

        $srv = new Vps_Srpc_Server();
        $srv->setHandler('Vps_Srpc_TestClasses_Handler');
        $this->assertEquals($srv->getHandler(), $compare);
    }

    public function testHandle()
    {
        $_REQUEST['method'] = 'find';
        $_REQUEST['arguments'] = array(3);
        $srv = new Vps_Srpc_Server(array(
            'handler' => new Vps_Srpc_TestClasses_Handler(),
            'returnResponse' => true
        ));
        $result = $srv->handle('getRow', array(3));

        $this->assertEquals(serialize(array('id' => 3, 'name' => 'Hans')), $result);
    }
}
