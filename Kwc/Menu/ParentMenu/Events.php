<?php
class Kwc_Menu_ParentMenu_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $menuClass = Kwc_Abstract::getSetting($this->_class, 'menuComponentClass');

        $ret[] = array(
            'class' => $menuClass,
            'event' => 'Kwf_Component_Event_ComponentClass_ContentChanged',
            'callback' => 'onMenuComponentClassContentChanged'
        );

        $ret[] = array(
            'class' => $menuClass,
            'event' => 'Kwf_Component_Event_Component_ContentChanged',
            'callback' => 'onMenuContentChanged'
        );
        $ret[] = array(
            'class' => $menuClass,
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onMenuHasContentChanged'
        );

        $alternativeComponents = call_user_func(array($menuClass, 'getAlternativeComponents'), $menuClass);
        $ret[] = array(
            'class' => $alternativeComponents['otherCategory'],
            'event' => 'Kwf_Component_Event_Component_ContentChanged',
            'callback' => 'onMenuContentChanged'
        );
        $ret[] = array(
            'class' => $alternativeComponents['otherCategory'],
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onMenuHasContentChanged'
        );
        return $ret;
    }

    public function onMenuComponentClassContentChanged(Kwf_Component_Event_ComponentClass_ContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged(
            $this->_class
        ));
    }

    public function onMenuContentChanged(Kwf_Component_Event_Component_ContentChanged $event)
    {
        $c = $event->component;
        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
            $this->_class, $c
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
