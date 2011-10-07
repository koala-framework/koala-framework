<?php
class Kwc_Paragraphs_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $generators = Kwc_Abstract::getSetting($this->_class, 'generators');
        foreach ($generators['paragraphs']['component'] as $component) {
            $ret[] = array(
                'class' => $component,
                'event' => 'Kwf_Component_Event_Component_HasContentChanged',
                'callback' => 'onChildHasContentChange'
            );
        }
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'childModel'),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'childModel'),
            'event' => 'Kwf_Component_Event_Row_Inserted',
            'callback' => 'onRowInsertOrDelete'
        );
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'childModel'),
            'event' => 'Kwf_Component_Event_Row_Deleted',
            'callback' => 'onRowInsertOrDelete'
        );
        return $ret;
    }

    public function onChildHasContentChange(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
            $this->_class, $event->getParentComponentId($event->dbId)
        ));
    }

    public function onRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
            $this->_class, $event->row->component_id
        ));
        if ($event->isDirty('visible')) {
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $event->row->component_id
            ));
        }
    }

    public function onRowInsertOrDelete(Kwf_Component_Event_Row_Abstract $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
            $this->_class, $event->row->component_id
        ));
        $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
            $this->_class, $event->row->component_id
        ));
    }
}
