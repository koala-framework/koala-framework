<?php
class Kwc_Basic_LinkTag_Events extends Kwc_Abstract_Cards_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach (Kwc_Abstract::getChildComponentClasses($this->_class, 'child') as $class) {
            $ret[] = array(
                'class' => $class,
                'event' => 'Kwf_Component_Event_Component_ContentChanged',
                'callback' => 'onChildContentChanged'
            );
        }
        return $ret;
    }

    public function onChildContentChanged(Kwf_Component_Event_Component_ContentChanged $event)
    {
        $component = $event->component;
        $parent = $component->parent;
        if ($parent->componentClass == $this->_class && $parent->isPage) {
            $this->fireEvent(
                new Kwf_Component_Event_Page_UrlChanged($this->_class, $parent)
            );
        }
    }
}
