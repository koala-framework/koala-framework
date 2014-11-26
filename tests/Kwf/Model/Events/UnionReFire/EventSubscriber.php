<?php
class Kwf_Model_Events_UnionReFire_EventSubscriber extends Kwf_Events_Subscriber
{
    public static $onUnionRowUpdatedCalled;
    public static $onSourceRowUpdatedCalled;


    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwf_Model_Events_UnionReFire_UnionModel',
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onUnionRowUpdated'
        );
        $ret[] = array(
            'class' => 'Kwf_Model_Events_UnionReFire_SourceModel',
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onSourceRowUpdated'
        );
        return $ret;
    }

    public function onUnionRowUpdated(Kwf_Events_Event_Row_Updated $ev)
    {
        self::$onUnionRowUpdatedCalled[] = $ev->row->id;
    }

    public function onSourceRowUpdated(Kwf_Events_Event_Row_Updated $ev)
    {
        self::$onSourceRowUpdatedCalled[] = $ev->row->id;
    }
}
