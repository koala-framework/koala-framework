<?php
class Kwf_Model_Union_Events extends Kwf_Model_EventSubscriber
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $m = Kwf_Model_Abstract::getInstance($this->_modelClass);
        foreach ($m->getUnionModels()as $k=>$sourceModel) {
            $sourceModelClass = get_class($sourceModel);
            $ret[] = array(
                'class' => $sourceModelClass,
                'event' => 'Kwf_Events_Event_Row_Inserted',
                'callback' => 'onRowEvent'
            );
            $ret[] = array(
                'class' => $sourceModelClass,
                'event' => 'Kwf_Events_Event_Row_Deleted',
                'callback' => 'onRowEvent'
            );
            $ret[] = array(
                'class' => $sourceModelClass,
                'event' => 'Kwf_Events_Event_Row_Updated',
                'callback' => 'onRowEvent'
            );
            $ret[] = array(
                'class' => $sourceModelClass,
                'event' => 'Kwf_Events_Event_Model_Updated',
                'callback' => 'onModelEvent'
            );
        }
        return $ret;
    }

    public function onRowEvent(Kwf_Events_Event_Row_Abstract $ev)
    {
        $eventCls = get_class($ev);
        $unionModel = Kwf_Model_Abstract::getInstance($this->_modelClass);
        $sourceRow = $ev->row;
        $sourceModel = $sourceRow->getModel();
        foreach ($unionModel->getUnionModels() as $modelKey => $m) {
            if ($m === $sourceModel) {
                $unionRow = $unionModel->getRow($modelKey.$sourceRow->{$sourceModel->getPrimaryKey()});
                $this->fireEvent(new $eventCls($unionRow));
            }
        }
    }

    public function onModelEvent(Kwf_Events_Event_Model_Updated $ev)
    {
        $eventCls = get_class($ev);
        $proxyModel = Kwf_Model_Abstract::getInstance($this->_modelClass);
        $this->fireEvent(new $eventCls($proxyModel));
    }
}
