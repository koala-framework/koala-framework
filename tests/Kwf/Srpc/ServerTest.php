<?php
/**
 * @group Service
 * @group Srpc_Server
 */
class Kwf_Srpc_ServerTest extends Kwf_Test_TestCase
{
    private $_client;

    public function setUp()
    {
        parent::setUp();
        $this->_client = $this->getMock('Kwf_Srpc_Client', array('_performRequest'));
    }

    public function testNoClassSetted()
    {
        $srv = new Kwf_Srpc_Server(array('returnResponse' => true));
        $response = $srv->handle();
        $this->assertContains('A handler has to be set', $response);
    }

    public function testNoMethodTransmitted()
    {
        $srv = new Kwf_Srpc_Server(array(
            'handler' => 'Kwf_Srpc_TestClasses_Handler',
            'returnResponse' => true
        ));
        $response = $srv->handle(null, array(3));
        $this->assertContains('\'method\' must be set as first argument', $response);
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
        $srv = new Kwf_Srpc_Server(array(
            'handler' => 'Kwf_Srpc_TestClasses_Handler',
            'returnResponse' => true
        ));
        $response = $srv->handle('getPrimaryKey');
        $this->assertEquals(serialize('my_id'), $response);
    }

    public function testSetClass()
    {
        $compare = new Kwf_Srpc_TestClasses_Handler();

        $srv = new Kwf_Srpc_Server(array('handler' => new Kwf_Srpc_TestClasses_Handler()));
        $this->assertEquals($srv->getHandler(), $compare);

        $srv = new Kwf_Srpc_Server(array('handler' => 'Kwf_Srpc_TestClasses_Handler'));
        $this->assertEquals($srv->getHandler(), $compare);

        $srv = new Kwf_Srpc_Server();
        $srv->setHandler('Kwf_Srpc_TestClasses_Handler');
        $this->assertEquals($srv->getHandler(), $compare);
    }

    public function testHandle()
    {
        $srv = new Kwf_Srpc_Server(array(
            'handler' => new Kwf_Srpc_TestClasses_Handler(),
            'returnResponse' => true
        ));
        $result = $srv->handle('getRow', array(3));

        $this->assertEquals(serialize(array('id' => 3, 'name' => 'Hans')), $result);
    }
}
