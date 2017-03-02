<?php
class Kwc_Form_Dynamic_Form_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach ($this->_getCreatingClasses($this->_class) as $class) {
            $ret[] = array(
                'class' => $class,
                'event' => 'Kwf_Component_Event_Component_ContentChanged',
                'callback' => 'onContentChange'
            );
        }
        return $ret;
    }

    public function onContentChange(Kwf_Events_Event_Abstract $event)
    {
        $c = $event->component->getChildComponent('-form');
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $c));
    }
}
