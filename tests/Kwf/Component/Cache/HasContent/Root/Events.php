<?php
class Vps_Component_Cache_HasContent_Root_Events extends Vps_Component_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'class' => 'Vps_Component_Cache_HasContent_Root_Child_Component',
            'event' => 'Vps_Component_Event_Component_HasContentChanged',
            'callback' => 'onChildHasContentChange'
        );
        return $ret;
    }

    public function onChildHasContentChange(Vps_Component_Event_Component_HasContentChanged $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
            $this->_class, str_replace('_child', '', $event->dbId)
        ));
    }
}
