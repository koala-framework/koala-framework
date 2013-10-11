<?php
class Kwc_Editable_Events extends Kwf_Component_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => Kwc_Abstract::getChildComponentClass($this->_class, 'content'),
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onContentHasContentChange'
        );
        return $ret;
    }

    public function onContentHasContentChange(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        if ($event->component->parent->componentClass == $this->_class) {
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $event->component->parent
            ));
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $event->component->parent
            ));
        }
    }
}
