<?php
class Kwc_Menu_ParentMenu_Events extends Kwc_Abstract_Events
{
    protected $_menuComponentClass;

    protected function _init()
    {
        parent::_init();
        $this->_initSettings();
    }

    //overwritten in Kwc_Menu_ParentMenu_Trl_Events
    protected function _initSettings()
    {
        $this->_menuComponentClass = $menuLevel = Kwc_Abstract::getSetting($this->_class, 'menuComponentClass');
    }

    public function getListeners()
    {
        $ret = parent::getListeners();

        $ret[] = array(
            'class' => $this->_menuComponentClass,
            'event' => 'Kwf_Component_Event_ComponentClass_ContentChanged',
            'callback' => 'onMenuComponentClassContentChanged'
        );

        $ret[] = array(
            'class' => $this->_menuComponentClass,
            'event' => 'Kwf_Component_Event_Component_ContentChanged',
            'callback' => 'onMenuContentChanged'
        );
        $ret[] = array(
            'class' => $this->_menuComponentClass,
            'event' => 'Kwf_Component_Event_Component_HasContentChanged',
            'callback' => 'onMenuHasContentChanged'
        );

        $c = $this->_menuComponentClass;
        $alternativeComponents = call_user_func(array(strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c, 'getAlternativeComponents'), $this->_menuComponentClass);
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
            $this->_class, $event->subroot
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
