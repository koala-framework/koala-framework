<?php
class Vpc_Abstract_Events extends Vps_Component_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        if (Vpc_Abstract::hasSetting($this->_class, 'ownModel')) {
            $ret[] = array(
                'class' => Vpc_Abstract::getSetting($this->_class, 'ownModel'),
                'event' => 'Vps_Component_Event_Row_Updated',
                'callback' => 'onOwnRowUpdate'
            );
        }
        if (Vpc_Admin::getComponentFile($this->_class, 'Master', 'tpl')) {
            //p('A');
        }
        return $ret;
    }

    public function onOwnRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
            $this->_class, $event->row->component_id
        ));
        if (Vpc_Abstract::hasSetting($this->_class, 'throwHasContentChangedOnRowColumnsUpdate')) {
            $columns = Vpc_Abstract::hasSetting($this->_class, 'throwHasContentChangedOnRowColumnsUpdate');
            if ($event->isDirty($columns)) {
                $this->fireEvent(new Vps_Component_Event_Component_HasContentChanged(
                    $this->_class, $event->row->component_id
                ));
            }
        }
    }
}
