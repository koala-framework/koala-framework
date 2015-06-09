<?php
class Kwf_Model_Events_ProxySourceNoClass_EventSubscriber extends Kwf_Events_Subscriber
{
    public static $onProxyRowInsertedCalled;
    public static $onProxyModelUpdatedCalled;

    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwf_Model_Events_ProxySourceNoClass_Model',
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onProxyRowInserted'
        );
        $ret[] = array(
            'class' => 'Kwf_Model_Events_ProxySourceNoClass_Model',
            'event' => 'Kwf_Events_Event_Model_Updated',
            'callback' => 'onProxyModelUpdated'
        );
        return $ret;
    }

    public function onProxyRowInserted(Kwf_Events_Event_Row_Inserted $ev)
    {
        self::$onProxyRowInsertedCalled++;
    }

    public function onProxyModelUpdated(Kwf_Events_Event_Model_Updated $ev)
    {
        self::$onProxyModelUpdatedCalled++;
    }
}
