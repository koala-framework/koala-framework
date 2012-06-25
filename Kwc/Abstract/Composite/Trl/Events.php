<?php
class Kwc_Abstract_Composite_Trl_Events extends Kwc_Chained_Trl_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach (Kwc_Abstract::getChildComponentClasses($this->_class, 'child') as $class) {
            $ret[] = array(
                'class' => $class,
                'event' => 'Kwf_Component_Event_Component_HasContentChanged',
                'callback' => 'onChildHasContentChange'
            );
        }
        return $ret;
    }

    public function onChildHasContentChange(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        if ($event->component->parent->componentClass == $this->_class) {
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $event->component->parent
            ));
            // TODO: parse template to check if ContentChanged is necessary
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $event->component->parent
            ));
        }
    }
}
