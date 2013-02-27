<?php
class Kwc_Abstract_List_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
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
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'childModel'),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'childModel'),
            'event' => 'Kwf_Component_Event_Model_Updated',
            'callback' => 'onModelUpdate'
        );
        $generators = Kwc_Abstract::getSetting($this->_class, 'generators');
        $ret[] = array(
            'class' => $generators['child']['component'],
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onChildHasContentChange'
        );
        return $ret;
    }

    public function onRowInsertOrDelete(Kwf_Component_Event_Row_Abstract $event)
    {
        foreach ($this->_getComponentsByRow($event->row) as $c) {
            if ($c->componentClass == $this->_class) {
                if ($event->row->visible) {
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

    public function onRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        foreach ($this->_getComponentsByRow($event->row) as $c) {
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

    protected function _getComponentsByRow($row)
    {
        return Kwf_Component_Data_Root::getInstance()->getComponentsByDbId(
            $row->component_id, array('ignoreVisible'=>true)
        );
    }

    public function onModelUpdate(Kwf_Component_Event_Model_Updated $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged(
            $this->_class
        ));
    }

    public function onChildHasContentChange(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $c = $event->component->parent;
        if ($c->componentClass == $this->_class) {
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $event->component->parent
            ));
        }
    }
}
