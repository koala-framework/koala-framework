<?php
class Vpc_Abstract_List_Trl_Events extends Vpc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => Vpc_Abstract::getSetting($this->_class, 'childModel'),
            'event' => 'Vps_Component_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        return $ret;
    }

    public function onRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        if ($event->isDirty('visible')) {
            $this->fireEvent(new Vps_Component_Event_Component_HasContentChanged(
                $this->_class, $event->row->component_id
            ));
        }
    }
}
