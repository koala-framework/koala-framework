<?php
class Kwc_Abstract_Composite_Events extends Kwc_Abstract_Events
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
        $classes = Kwc_Abstract::getChildComponentClasses($this->_class, 'child');
        $key = array_search($event->class, $classes);
        if ($key && 
            substr($event->dbId, -strlen($key)-1) == '-' . $key && 
            $event->getParentDbId()
        ) {
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $event->getParentDbId()
            ));
        }
    }
}
