<?php
class Kwc_Advanced_DownloadsTree_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'downloadsModel'),
            'event' => 'Kwf_Component_Event_Row_Inserted',
            'callback' => 'onRowInsertOrDelete'
        );
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'downloadsModel'),
            'event' => 'Kwf_Component_Event_Row_Deleted',
            'callback' => 'onRowInsertOrDelete'
        );
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'downloadsModel'),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        return $ret;
    }

    public function onRowInsertOrDelete(Kwf_Component_Event_Row_Abstract $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
            $this->_class, $event->row->getParentRow('Project')->component_id
        ));
    }

    public function onRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        if ($event->isDirty('visible')) {
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $event->row->getParentRow('Project')->component_id
            ));
        }
    }
}
