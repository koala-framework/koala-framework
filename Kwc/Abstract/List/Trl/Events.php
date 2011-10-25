<?php
class Kwc_Abstract_List_Trl_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'childModel'),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        return $ret;
    }

    public function onRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        if ($event->isDirty('visible')) {
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $event->row->component_id
            ));
        }
    }
}
