<?php
class Vpc_Basic_Flash_Upload_Events extends Vpc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => Vpc_Abstract::getSetting($this->_class, 'ownModel'),
            'event' => 'Vps_Component_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        return $ret;
    }

    public function onRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        if ($event->isDirty('vps_upload_id_media')) {
            $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
                $this->_class, $event->row->component_id
            ));
        }
    }
}
