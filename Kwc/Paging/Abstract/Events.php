<?php
class Kwc_Paging_Abstract_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            foreach (Kwc_Abstract::getChildComponentClasses($class) as $childClass) {
                if ($childClass == $this->_class) {
                    $ret[] = array(
                        'class' => $class,
                        'event' => 'Kwf_Component_Event_ComponentClass_ContentChanged',
                        'callback' => 'onParentContentChanged'
                    );
                    $ret[] = array(
                        'class' => $class,
                        'event' => 'Kwf_Component_Event_ComponentClass_AllPartialsChanged',
                        'callback' => 'onParentPartialsChanged'
                    );
                }
            }
        }
        return $ret;
    }

    public function onParentContentChanged(Kwf_Component_Event_ComponentClass_ContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged($this->_class));
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_AllPartialChanged($this->_class));
    }

    public function onParentPartialsChanged(Kwf_Component_Event_ComponentClass_AllPartialsChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged($this->_class, $event->subroot));
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_AllPartialsChanged($this->_class, $event->subroot));
    }
}
