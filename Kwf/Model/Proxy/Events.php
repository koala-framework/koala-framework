<?php
class Kwf_Model_Proxy_Events extends Kwf_Model_EventSubscriber
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $factoryId = $this->_getModel()->getProxyModel()->getFactoryId();
        $ret[] = array(
            'class' => $factoryId,
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onRowEvent'
        );
        $ret[] = array(
            'class' => $factoryId,
            'event' => 'Kwf_Events_Event_Row_Deleted',
            'callback' => 'onRowEvent'
        );
        $ret[] = array(
            'class' => $factoryId,
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onRowEvent'
        );
        $ret[] = array(
            'class' => $factoryId,
            'event' => 'Kwf_Events_Event_Model_Updated',
            'callback' => 'onModelEvent'
        );
        return $ret;
    }

    public function onRowEvent(Kwf_Events_Event_Row_Abstract $ev)
    {
        $eventCls = get_class($ev);
        $sourceRow = $ev->row;
        $sourceModel = $sourceRow->getModel();
        $proxyRow = $this->_getModel()->getRow($sourceRow->{$sourceModel->getPrimaryKey()});
        $this->fireEvent(new $eventCls($proxyRow));
    }

    public function onModelEvent(Kwf_Events_Event_Model_Updated $ev)
    {
        $eventCls = get_class($ev);
        $this->fireEvent(new $eventCls($this->_getModel(), $ev->ids));
    }
}
