<?php
class Kwf_Component_Cache_HasContent_Root_Child_Events extends Kwf_Component_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'class' => 'Kwf_Component_Cache_HasContent_Root_Child_Model',
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        return $ret;
    }

    public function onRowUpdate(Kwf_Events_Event_Row_Updated $event)
    {
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($event->row->component_id) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $c
            ));
        }
    }
}
