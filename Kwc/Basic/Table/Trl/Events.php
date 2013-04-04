<?php
class Kwc_Basic_Table_Trl_Events extends Kwc_Chained_Trl_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $childModel = Kwc_Abstract::getSetting($this->_class, 'childModel');
        $ret[] = array(
            'class' => $childModel,
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onOwnRowEvent'
        );
        $ret[] = array(
            'class' => $childModel,
            'event' => 'Kwf_Component_Event_Row_Deleted',
            'callback' => 'onOwnRowEvent'
        );
        $ret[] = array(
            'class' => $childModel,
            'event' => 'Kwf_Component_Event_Row_Inserted',
            'callback' => 'onOwnRowEvent'
        );
        return $ret;
    }

    public function onOwnRowEvent(Kwf_Component_Event_Row_Abstract $event)
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $event->row->component_id, array('ignoreVisible'=>true)
        );
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
            $this->_class, $component
        ));
    }

    public function onMasterContentChanged(Kwf_Component_Event_Component_ContentChanged $event)
    {
        $components = Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->component, 'Trl');
        foreach ($components as $component) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $component->componentClass, $component
            ));
        }
    }
}
