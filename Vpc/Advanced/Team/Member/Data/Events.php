<?php
class Vpc_Advanced_Team_Member_Data_Events extends Vpc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => Vpc_Abstract::getSetting($this->_class, 'ownModel'),
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
    }
}
