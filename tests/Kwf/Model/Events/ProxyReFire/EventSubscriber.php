<?php
class Kwf_Model_Events_ProxyReFire_EventSubscriber extends Kwf_Events_Subscriber
{
    public static $onProxyRowInsertedCalled;
    public static $onProxyProxyRowInsertedCalled;
    public static $onSourceRowInsertedCalled;
    public static $onProxyModelUpdatedCalled;
    public static $onProxyProxyModelUpdatedCalled;
    public static $onSourceModelUpdatedCalled;

    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwf_Model_Events_ProxyReFire_ProxyModel',
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onProxyRowInserted'
        );
        $ret[] = array(
            'class' => 'Kwf_Model_Events_ProxyReFire_ProxyProxyModel',
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onProxyProxyRowInserted'
        );
        $ret[] = array(
            'class' => 'Kwf_Model_Events_ProxyReFire_SourceModel',
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onSourceRowInserted'
        );

        $ret[] = array(
            'class' => 'Kwf_Model_Events_ProxyReFire_ProxyModel',
            'event' => 'Kwf_Events_Event_Model_Updated',
            'callback' => 'onProxyModelUpdated'
        );
        $ret[] = array(
            'class' => 'Kwf_Model_Events_ProxyReFire_ProxyProxyModel',
            'event' => 'Kwf_Events_Event_Model_Updated',
            'callback' => 'onProxyProxyModelUpdated'
        );
        $ret[] = array(
            'class' => 'Kwf_Model_Events_ProxyReFire_SourceModel',
            'event' => 'Kwf_Events_Event_Model_Updated',
            'callback' => 'onSourceModelUpdated'
        );
        return $ret;
    }

    public function onProxyRowInserted(Kwf_Events_Event_Row_Inserted $ev)
    {
        self::$onProxyRowInsertedCalled++;
    }

    public function onProxyProxyRowInserted(Kwf_Events_Event_Row_Inserted $ev)
    {
        self::$onProxyProxyRowInsertedCalled++;
    }

    public function onSourceRowInserted(Kwf_Events_Event_Row_Inserted $ev)
    {
        self::$onSourceRowInsertedCalled++;
    }

    public function onProxyModelUpdated(Kwf_Events_Event_Model_Updated $ev)
    {
        self::$onProxyModelUpdatedCalled++;
    }

    public function onProxyProxyModelUpdated(Kwf_Events_Event_Model_Updated $ev)
    {
        self::$onProxyProxyModelUpdatedCalled++;
    }

    public function onSourceModelUpdated(Kwf_Events_Event_Model_Updated $ev)
    {
        self::$onSourceModelUpdatedCalled++;
    }
}
