<?php
class Vpc_Paragraphs_Events extends Vpc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $generators = Vpc_Abstract::getSetting($this->_class, 'generators');
        foreach ($generators['paragraphs']['component'] as $component) {
            $ret[] = array(
                'class' => $component,
                'event' => 'Vps_Component_Event_Component_HasContentChanged',
                'callback' => 'onChildHasContentChange'
            );
        }
        $ret[] = array(
            'class' => Vpc_Abstract::getSetting($this->_class, 'childModel'),
            'event' => 'Vps_Component_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
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
        return $ret;
    }

    public function onChildHasContentChange(Vps_Component_Event_Component_HasContentChanged $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_HasContentChanged(
            $this->_class, $event->getParentComponentId($event->dbId)
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

    public function onRowInsertOrDelete(Vps_Component_Event_Row_Abstract $event)
    {
        $this->fireEvent(new Vps_Component_Event_Component_ContentChanged(
            $this->_class, $event->row->component_id
        ));
        $this->fireEvent(new Vps_Component_Event_Component_HasContentChanged(
            $this->_class, $event->row->component_id
        ));
    }
}
