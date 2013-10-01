<?php
class Kwc_Abstract_Cards_Events extends Kwc_Abstract_Composite_Events // extends composite for childHasContent handlers
{
    protected function _onOwnRowUpdate(Kwf_Component_Data $c, Kwf_Component_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdate($c, $event);
        if ($event->isDirty('component')) {
            $generators = Kwc_Abstract::getSetting($this->_class, 'generators');
            $classes = $generators['child']['component'];
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveRemoved(
                $this->_getClassFromRow($classes, $event->row, true), $c)
            );
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveAdded(
                $this->_getClassFromRow($classes, $event->row, false), $c)
            );
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $c)
            );
        }
    }
}
