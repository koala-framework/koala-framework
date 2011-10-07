<?php
class Vps_Component_Cache_HasContent_Root_Child_Events extends Vps_Component_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'class' => 'Vps_Component_Cache_HasContent_Root_Child_Model',
            'event' => 'Vps_Component_Event_Row_Updated',
            'callback' => 'onOwnRowUpdate'
        );
        return $ret;
    }

    public function onOwnRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_HasContentChanged(
            $this->_class, $event->row->component_id
        ));
    }
}
