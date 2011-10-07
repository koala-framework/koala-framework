<?php
class Kwf_Component_Cache_HasContent_Root_Child_Events extends Kwf_Component_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'class' => 'Kwf_Component_Cache_HasContent_Root_Child_Model',
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onOwnRowUpdate'
        );
        return $ret;
    }

    public function onOwnRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
            $this->_class, $event->row->component_id
        ));
    }
}
