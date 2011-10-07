<?php
class Vpc_Advanced_DownloadsTree_Events extends Vpc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => Vpc_Abstract::getSetting($this->_class, 'downloadsModel'),
            'event' => 'Vps_Component_Event_Row_Inserted',
            'callback' => 'onRowInsertOrDelete'
        );
        $ret[] = array(
            'class' => Vpc_Abstract::getSetting($this->_class, 'downloadsModel'),
            'event' => 'Vps_Component_Event_Row_Deleted',
            'callback' => 'onRowInsertOrDelete'
        );
        $ret[] = array(
            'class' => Vpc_Abstract::getSetting($this->_class, 'downloadsModel'),
            'event' => 'Vps_Component_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        return $ret;
    }

    public function onRowInsertOrDelete(Vps_Component_Event_Row_Abstract $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_HasContentChanged(
            $this->_class, $event->row->getParentRow('Project')->component_id
        ));
    }

    public function onRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        if ($event->isDirty('visible')) {
            $this->fireEvent(new Vps_Component_Event_Component_HasContentChanged(
                $this->_class, $event->row->getParentRow('Project')->component_id
            ));
        }
    }
}
