<?php
class Kwc_Abstract_Cards_Events extends Kwc_Abstract_Composite_Events // extends composite for childHasContent handlers
{
    protected function _onOwnRowUpdate(Kwf_Component_Data $c, Kwf_Component_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdate($c, $event);
        if ($event->isDirty('component')) {
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveRemoved(
                $this->_getClassFromRow($event->row, true), $c->componentId)
            );
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveAdded(
                $this->_getClassFromRow($event->row, false), $c->componentId)
            );
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $event->row->component_id)
            );
        }
    }

    protected function _getClassFromRow($row, $cleanValue = false)
    {
        $generators = Kwc_Abstract::getSetting($this->_class, 'generators');
        $classes = $generators['child']['component'];
        if ($cleanValue) {
            $c = $row->getCleanValue('component');
        } else {
            $c = $row->component;
        }
        if (isset($classes[$c])) {
            return $classes[$row->component];
        }
        $class = array_shift($classes);
        return $class;
    }
}
