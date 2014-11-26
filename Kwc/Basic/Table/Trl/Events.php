<?php
class Kwc_Basic_Table_Trl_Events extends Kwc_Chained_Trl_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $m = Kwc_Abstract::createChildModel($this->_class);
        $ret[] = array(
            'class' => $m,
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onOwnRowEvent'
        );
        $ret[] = array(
            'class' => $m,
            'event' => 'Kwf_Events_Event_Row_Deleted',
            'callback' => 'onOwnRowEvent'
        );
        $ret[] = array(
            'class' => $m,
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onOwnRowEvent'
        );
        return $ret;
    }

    public function onOwnRowEvent(Kwf_Events_Event_Row_Abstract $event)
    {
        $components = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId(
            $event->row->component_id, array('ignoreVisible'=>true)
        );
        foreach ($components as $component) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $component
            ));
        }
    }

    public function onMasterContentChanged(Kwf_Component_Event_Component_ContentChanged $event)
    {
        parent::onMasterContentChanged($event);
        $components = Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->component, 'Trl');
        foreach ($components as $component) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $component->componentClass, $component
            ));
        }
    }
}
