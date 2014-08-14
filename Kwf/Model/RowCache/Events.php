<?php
class Kwf_Model_RowCache_Events extends Kwf_Model_EventSubscriber
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => $this->_modelClass,
            'event' => 'Kwf_Events_Event_Row_Deleted',
            'callback' => 'onRowEvent'
        );
        $ret[] = array(
            'class' => $this->_modelClass,
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onRowEvent'
        );
        $ret[] = array(
            'class' => $this->_modelClass,
            'event' => 'Kwf_Events_Event_Model_Updated',
            'callback' => 'onModelEvent'
        );
        return $ret;
    }

    public function onRowEvent(Kwf_Events_Event_Row_Abstract $ev)
    {
        $m = Kwf_Model_Abstract::getInstance($this->_modelClass);
        $m->clearRowCache($ev->row->{$m->getPrimaryKey()});
    }

    public function onModelEvent(Kwf_Events_Event_Model_Updated $ev)
    {
        //TODO
    }
}
