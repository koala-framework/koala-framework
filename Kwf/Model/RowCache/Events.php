<?php
class Kwf_Model_RowCache_Events extends Kwf_Model_EventSubscriber
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $factoryId = $this->_getModel()->getProxyModel()->getFactoryId();
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
        $m = $this->_getModel();
        $m->clearRowCache($ev->row->{$m->getPrimaryKey()});
    }

    public function onModelEvent(Kwf_Events_Event_Model_Updated $ev)
    {
        $m = $this->_getModel();
        if (is_array($ev->ids)) {
            foreach ($ev->ids as $id) {
                $m->clearRowCache($id);
            }
        } else {
            //this is very inefficient as we have to iterate all ids
            //but there is no way to delete all ids
            $s = new Kwf_Model_Select();
            $m->clearRowsCache($s);
        }
    }
}
