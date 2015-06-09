<?php
class Kwf_Model_Events_ProxyReFire_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyRowInsertedCalled = 0;
        Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyProxyRowInsertedCalled = 0;
        Kwf_Model_Events_ProxyReFire_EventSubscriber::$onSourceRowInsertedCalled = 0;
        Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyModelUpdatedCalled = 0;
        Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyProxyModelUpdatedCalled = 0;
        Kwf_Model_Events_ProxyReFire_EventSubscriber::$onSourceModelUpdatedCalled = 0;
        Kwf_Events_Dispatcher::addListeners('Kwf_Model_Events_ProxyReFire_ProxyProxyModel');
        Kwf_Events_Dispatcher::addListeners(Kwf_Events_Subscriber::getInstance('Kwf_Model_Events_ProxyReFire_EventSubscriber'));
    }

    //easy. row is created by proxy, proxy fires event
    public function testProxyProxyEvent()
    {
        $row = Kwf_Model_Abstract::getInstance('Kwf_Model_Events_ProxyReFire_ProxyModel')->createRow();
        $row->foo = 'bar';
        $row->save();
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyRowInsertedCalled);
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyProxyRowInsertedCalled);
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onSourceRowInsertedCalled);
    }

    //now row is created in source model, proxy doesn't know about it
    public function testSourceProxyEvent()
    {
        $row = Kwf_Model_Abstract::getInstance('Kwf_Model_Events_ProxyReFire_SourceModel')->createRow();
        $row->foo = 'bar';
        $row->save();
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyRowInsertedCalled);
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyProxyRowInsertedCalled);
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onSourceRowInsertedCalled);
    }

    //now row is created in proxy model, source should also fire events
    public function testProxySourceEvent()
    {
        $row = Kwf_Model_Abstract::getInstance('Kwf_Model_Events_ProxyReFire_ProxyProxyModel')->createRow();
        $row->foo = 'bar';
        $row->save();
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyRowInsertedCalled);
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyProxyRowInsertedCalled);
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onSourceRowInsertedCalled);
    }






    public function testProxySourceEventModel()
    {
        Kwf_Model_Abstract::getInstance('Kwf_Model_Events_ProxyReFire_SourceModel')->import(Kwf_Model_Abstract::FORMAT_ARRAY, array(
            array('foo'=>'bar')
        ));
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyModelUpdatedCalled);
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyProxyModelUpdatedCalled);
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onSourceModelUpdatedCalled);
    }

    public function testSourceProxyEventModel()
    {
        Kwf_Model_Abstract::getInstance('Kwf_Model_Events_ProxyReFire_ProxyModel')->import(Kwf_Model_Abstract::FORMAT_ARRAY, array(
            array('foo'=>'bar')
        ));
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyModelUpdatedCalled);
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyProxyModelUpdatedCalled);
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onSourceModelUpdatedCalled);
    }

    public function testProxyProxySourceEventModel()
    {
        Kwf_Model_Abstract::getInstance('Kwf_Model_Events_ProxyReFire_ProxyProxyModel')->import(Kwf_Model_Abstract::FORMAT_ARRAY, array(
            array('foo'=>'bar')
        ));
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyModelUpdatedCalled);
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onProxyProxyModelUpdatedCalled);
        $this->assertEquals(1, Kwf_Model_Events_ProxyReFire_EventSubscriber::$onSourceModelUpdatedCalled);
    }
}
