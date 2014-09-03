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
        $m = Kwc_Abstract::createChildModel($this->_class);
        $ret[] = array(
            'class' => $m,
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        $ret[] = array(
            'class' => $m,
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onRowInsertOrDelete'
        );
        $ret[] = array(
            'class' => $m,
            'event' => 'Kwf_Events_Event_Row_Deleted',
            'callback' => 'onRowInsertOrDelete'
        );
        return $ret;
    }

    public function onChildHasContentChange(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $c = $event->component;
        if ($c->componentClass == $this->_class) {
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $event->component->parent
            ));
        }
    }

    public function onRowUpdate(Kwf_Events_Event_Row_Updated $event)
    {
        foreach(Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($event->row->component_id) as $c) {
            if ($c->componentClass == $this->_class) {
                $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                    $this->_class, $c
                ));
                if ($event->isDirty('visible')) {
                    $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                        $this->_class, $c
                    ));
                }
            }
        }
    }

    public function onRowInsertOrDelete(Kwf_Events_Event_Row_Abstract $event)
    {
        foreach(Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($event->row->component_id) as $c) {
            if ($c->componentClass == $this->_class) {
                $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                    $this->_class, $c
                ));
                $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                    $this->_class, $c
                ));
            }
        }
    }
}
