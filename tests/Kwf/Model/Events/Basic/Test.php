<?php
class Kwf_Model_Events_Basic_Test extends Kwf_Test_TestCase
{

    public function setUp()
    {
        parent::setUp();
        Kwf_Model_Events_Basic_EventSubscriber::$onTestEventCalled = 0;
    }

    public function testIt()
    {
        Kwf_Events_Dispatcher::addListeners(Kwf_Model_EventSubscriber::getInstance('Kwf_Model_Events_Basic_EventSubscriber', array('modelClass' => 'Kwf_Model_Events_Basic_Model')));
        Kwf_Events_Dispatcher::fireEvent(new Kwf_Model_Events_Basic_TestEvent('Kwf_Model_Events_Basic_Test'));
        $this->assertEquals(Kwf_Model_Events_Basic_EventSubscriber::$onTestEventCalled, 1);
    }

    public function testAddModelListeners()
    {
        Kwf_Events_Dispatcher::addListeners('Kwf_Model_Events_Basic_Model');
        Kwf_Events_Dispatcher::fireEvent(new Kwf_Model_Events_Basic_TestEvent('Kwf_Model_Events_Basic_Test'));
        $this->assertEquals(Kwf_Model_Events_Basic_EventSubscriber::$onTestEventCalled, 1);
    }
}
