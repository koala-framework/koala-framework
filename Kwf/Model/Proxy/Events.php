<?php
class Kwf_Model_Proxy_Events extends Kwf_Model_EventSubscriber
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $m = Kwf_Model_Abstract::getInstance($this->_modelClass);
        $proxyModelClass = get_class($m->getProxyModel());
        $ret[] = array(
            'class' => $proxyModelClass,
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onRowEvent'
        );
        $ret[] = array(
            'class' => $proxyModelClass,
            'event' => 'Kwf_Events_Event_Row_Deleted',
            'callback' => 'onRowEvent'
        );
        $ret[] = array(
            'class' => $proxyModelClass,
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onRowEvent'
        );
        $ret[] = array(
            'class' => $proxyModelClass,
            'event' => 'Kwf_Events_Event_Model_Updated',
            'callback' => 'onModelEvent'
        );
        return $ret;
    }

    public function onRowEvent(Kwf_Events_Event_Row_Abstract $ev)
    {
        $eventCls = get_class($ev);
        $proxyModel = Kwf_Model_Abstract::getInstance($this->_modelClass);
        $sourceRow = $ev->row;
        $sourceModel = $sourceRow->getModel();
        $proxyRow = $proxyModel->getRow($sourceRow->{$sourceModel->getPrimaryKey()});
        $this->fireEvent(new $eventCls($proxyRow));
    }

    public function onModelEvent(Kwf_Events_Event_Model_Updated $ev)
    {
        $eventCls = get_class($ev);
        $proxyModel = Kwf_Model_Abstract::getInstance($this->_modelClass);
        $this->fireEvent(new $eventCls($proxyModel));
    }
}
