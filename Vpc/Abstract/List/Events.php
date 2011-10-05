<?php
class Vpc_Abstract_List_Events extends Vpc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => Vpc_Abstract::getSetting($this->_class, 'childModel'),
            'event' => 'Vps_Component_Event_Row_Inserted',
            'callback' => 'onRowInsertOrDelete'
        );
        $ret[] = array(
            'class' => Vpc_Abstract::getSetting($this->_class, 'childModel'),
            'event' => 'Vps_Component_Event_Row_Deleted',
            'callback' => 'onRowInsertOrDelete'
        );
        $ret[] = array(
            'class' => Vpc_Abstract::getSetting($this->_class, 'childModel'),
            'event' => 'Vps_Component_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        $generators = Vpc_Abstract::getSetting($this->_class, 'generators');
        $ret[] = array(
            'class' => $generators['child']['component'],
            'event' => 'Vps_Component_Event_Component_ContentChanged',
            'callback' => 'onChildContentChange'
        );
        return $ret;
    }

    public function onRowInsertOrDelete(Vps_Component_Event_Row_Abstract $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
            $this->_class, $event->row->component_id
        ));
        $this->fireEvent(new Vps_Component_Event_HasComponent_ContentChanged(
            $this->_class, $event->row->component_id
        ));
    }

    public function onRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
            $this->_class, $event->row->component_id
        ));
        if ($event->isDirty('visible')) {
            $this->fireEvent(new Vps_Component_Event_Component_HasContentChanged(
                $this->_class, $event->row->component_id
            ));
        }
    }

    public function onChildContentChange(Vps_Component_Event_Component_ContentChanged $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_HasContentChanged(
            $this->_class, $event->getParentComponentId($event->dbId)
        ));
    }
}
