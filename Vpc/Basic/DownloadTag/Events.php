<?php
class Vpc_Basic_DownloadTag_Events extends Vpc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => Vpc_Abstract::getSetting($this->_class, 'ownModel'),
            'event' => 'Vps_Component_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        $ret[] = array(
            'class' => Vpc_Abstract::getSetting($this->_class, 'ownModel'),
            'event' => 'Vps_Component_Event_Row_Inserted',
            'callback' => 'onRowUpdate'
        );
        return $ret;
    }

    public function onRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        if ($event->isDirty('vps_upload_id')) {
            $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
                $this->_class, $event->row->component_id
            ));
        }
    }
}
