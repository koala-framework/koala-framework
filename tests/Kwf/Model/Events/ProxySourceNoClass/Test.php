<?php
class Kwf_Model_Events_ProxySourceNoClass_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Model_Events_ProxySourceNoClass_EventSubscriber::$onProxyRowInsertedCalled = 0;
        Kwf_Model_Events_ProxySourceNoClass_EventSubscriber::$onProxyModelUpdatedCalled = 0;
        Kwf_Events_Dispatcher::addListeners('Kwf_Model_Events_ProxySourceNoClass_Model');
        Kwf_Events_Dispatcher::addListeners(Kwf_Events_Subscriber::getInstance('Kwf_Model_Events_ProxySourceNoClass_EventSubscriber'));
    }

    public function testProxySourceEvent()
    {
        $row = Kwf_Model_Abstract::getInstance('Kwf_Model_Events_ProxySourceNoClass_Model')->createRow();
        $row->foo = 'bar';
        $row->save();
        $this->assertEquals(1, Kwf_Model_Events_ProxySourceNoClass_EventSubscriber::$onProxyRowInsertedCalled);
    }

    public function testProxyProxySourceEventModel()
    {
        Kwf_Model_Abstract::getInstance('Kwf_Model_Events_ProxySourceNoClass_Model')->import(Kwf_Model_Abstract::FORMAT_ARRAY, array(
            array('foo'=>'bar')
        ));
        $this->assertEquals(1, Kwf_Model_Events_ProxySourceNoClass_EventSubscriber::$onProxyModelUpdatedCalled);
    }
}
