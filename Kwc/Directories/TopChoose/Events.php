<?php
class Kwc_Directories_TopChoose_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwc_Directories_TopChoose_Model',
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onRowUpdated'
        );
        return $ret;
    }

    public function onRowUpdated(Kwf_Events_Event_Row_Updated $event)
    {
        $this->fireEvent(new Kwc_Directories_List_EventDirectoryChanged($this->_class));
        /*
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($event->row->component_id) as $component) {
            foreach (Kwc_Directories_TopChoose_Component::getItemDirectoryClasses($component->componentClass) as $class) {
                $this->fireEvent(new Kwc_Directories_List_EventItemsUpdated($this->_class));
            }
        };
        */
    }
}
