<?php
class Kwf_Model_Events_UnionReFire_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Model_Events_UnionReFire_EventSubscriber::$onUnionRowUpdatedCalled = array();
        Kwf_Model_Events_UnionReFire_EventSubscriber::$onSourceRowUpdatedCalled = array();
        Kwf_Events_Dispatcher::addListeners('Kwf_Model_Events_UnionReFire_UnionModel');
        Kwf_Events_Dispatcher::addListeners(Kwf_Events_Subscriber::getInstance('Kwf_Model_Events_UnionReFire_EventSubscriber'));
    }

    //easy. row is created by proxy, proxy fires event
    public function testUnionEvent()
    {
        $row = Kwf_Model_Abstract::getInstance('Kwf_Model_Events_UnionReFire_UnionModel')->getRow('t1');
        $row->foo = 'bar';
        $row->save();
        $this->assertEquals(array('t1'), Kwf_Model_Events_UnionReFire_EventSubscriber::$onUnionRowUpdatedCalled);
        $this->assertEquals(array(1), Kwf_Model_Events_UnionReFire_EventSubscriber::$onSourceRowUpdatedCalled);
    }

    //now row is created in source model, proxy doesn't know about it
    public function testSourceEvent()
    {
        $row = Kwf_Model_Abstract::getInstance('Kwf_Model_Events_UnionReFire_SourceModel')->getRow(1);
        $row->foo = 'bar';
        $row->save();
        $this->assertEquals(array('t1'), Kwf_Model_Events_UnionReFire_EventSubscriber::$onUnionRowUpdatedCalled);
        $this->assertEquals(array(1), Kwf_Model_Events_UnionReFire_EventSubscriber::$onSourceRowUpdatedCalled);
    }
}
