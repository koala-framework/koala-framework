<?php
class Kwc_Menu_ParentContent_Events extends Kwc_Abstract_Events //not Kwc_Basic_ParentContent_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_ParentChanged',
            'callback' => 'onPageParentChanged'
        );
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'menuComponentClass'),
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onMenuHasContentChanged'
        );
        return $ret;
    }

    public function onPageParentChanged(Kwf_Component_Event_Page_ParentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged(
            $this->_class, $event->component
        ));
    }

    public function onMenuHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        $c = $event->component;
        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveHasContentChanged(
            $this->_class, $c
        ));
    }
}
