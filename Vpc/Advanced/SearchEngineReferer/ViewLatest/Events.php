<?php
class Vpc_Advanced_SearchEngineReferer_ViewLatest_Events extends Vpc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Vpc_Advanced_SearchEngineReferer_Model',
            'event' => 'Vps_Component_Event_Row_Inserted',
            'callback' => 'onRowInsert'
        );
        return $ret;
    }

    public function onRowInsert(Vps_Component_Event_Row_Inserted $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
            $this->_class, $event->row->component_id
        ));
        $this->fireEvent(new Vps_Component_Event_Component_HasContentChanged(
            $this->_class, $event->row->component_id
        ));
    }
}
