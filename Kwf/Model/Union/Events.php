<?php
class Kwf_Model_Union_Events extends Kwf_Model_EventSubscriber
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach ($this->_getModel()->getUnionModels()as $k=>$sourceModel) {
            $sourceModelId = $sourceModel->getFactoryId();
            $ret[] = array(
                'class' => $sourceModelId,
                'event' => 'Kwf_Events_Event_Row_Inserted',
                'callback' => 'onRowEvent'
            );
            $ret[] = array(
                'class' => $sourceModelId,
                'event' => 'Kwf_Events_Event_Row_Deleted',
                'callback' => 'onRowEvent'
            );
            $ret[] = array(
                'class' => $sourceModelId,
                'event' => 'Kwf_Events_Event_Row_Updated',
                'callback' => 'onRowEvent'
            );
            $ret[] = array(
                'class' => $sourceModelId,
                'event' => 'Kwf_Events_Event_Model_Updated',
                'callback' => 'onModelEvent'
            );
        }
        return $ret;
    }

    public function onRowEvent(Kwf_Events_Event_Row_Abstract $ev)
    {
        $eventCls = get_class($ev);
        $unionModel = $this->_getModel();
        $sourceRow = $ev->row;
        $sourceModel = $sourceRow->getModel();
        foreach ($unionModel->getUnionModels() as $modelKey => $m) {
            if ($m === $sourceModel) {
                $unionRow = $unionModel->getRow($modelKey.$sourceRow->{$sourceModel->getPrimaryKey()});
                $this->fireEvent(new $eventCls($unionRow));
                return;
            }
        }
        throw new Kwf_Exception("Didn't find sourceModel as unionModel");
    }

    public function onModelEvent(Kwf_Events_Event_Model_Updated $ev)
    {
        $eventCls = get_class($ev);
        $unionModel = $this->_getModel();
        $this->fireEvent(new $eventCls($unionModel));
    }
}
