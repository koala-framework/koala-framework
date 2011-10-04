<?php
/**
 * @group Service
 * @group Srpc_Server
 */
class Vps_Srpc_ServerTest extends Vps_Test_TestCase
{
    private $_client;

    public function setUp()
    {
        parent::setUp();
        $this->_client = $this->getMock('Vps_Srpc_Client', array('_performRequest'));
    }

    public function testNoClassSetted()
    {
        $srv = new Vps_Srpc_Server(array('returnResponse' => true));
        $response = $srv->handle();

        $exception = unserialize($response);
        if ($exception instanceof Vps_Exception_Serializable) {
            $exception = $exception->getException();
        }
        $this->assertTrue($exception instanceof Vps_Srpc_Exception);
    }

    public function testNoMethodTransmitted()
    {
        $srv = new Vps_Srpc_Server(array(
            'handler' => 'Vps_Srpc_TestClasses_Handler',
            'returnResponse' => true
        ));
        $response = $srv->handle(null, array(3));
        $exception = unserialize($response);
        if ($exception instanceof Vps_Exception_Serializable) {
            $exception = $exception->getException();
        }
        $this->assertTrue($exception instanceof Vps_Srpc_Exception);
    }

    // nötig weil der sonst den kompletten backtrace vom test ausgibt und da
    // sachen drinstehn die er nicht unserializen kann
    private function _matchSrpcServerException($response)
    {
        if (preg_match('#^O:[0-9]+:"([^"]+)":[0-9]+:\\{s:10:"\0\*\0message";s:[0-9]+:"([^"]+)"#', $response, $m)) {
            return new $m[1]($m[2]);
        }
        return null;
    }

    public function testNoArgumentsTransmitted()
    {
        $srv = new Vps_Srpc_Server(array(
            'handler' => 'Vps_Srpc_TestClasses_Handler',
            'returnResponse' => true
        ));
        $response = $srv->handle('getPrimaryKey');
        $this->assertEquals(serialize('my_id'), $response);
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
        $srv = new Vps_Srpc_Server(array(
            'handler' => new Vps_Srpc_TestClasses_Handler(),
            'returnResponse' => true
        ));
        $result = $srv->handle('getRow', array(3));

        $this->assertEquals(serialize(array('id' => 3, 'name' => 'Hans')), $result);
    }
}
