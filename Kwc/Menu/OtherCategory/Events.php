<?php
class Kwc_Menu_OtherCategory_Events extends Kwc_Abstract_Events
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
        return $ret;
    }

    public function onMenuComponentClassContentChanged(Kwf_Component_Event_ComponentClass_ContentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged(
            $this->_class, $event->subroot
        ));
    }

    private function _getOwnDatas($menu)
    {
        $ret = array();
        if (!$menu->parent->parent) return $ret;
        foreach ($menu->parent->parent->getChildComponents(array('flags' => array('menuCategory'=>true))) as $c) {
            $ret = array_merge($ret, $c->getChildComponents(array('componentClass' => $this->_class)));
        }
        return $ret;
    }

    public function onMenuContentChanged(Kwf_Component_Event_Component_ContentChanged $event)
    {
        foreach ($this->_getOwnDatas($event->component) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $c
            ));
        }
    }

    public function onMenuHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        foreach ($this->_getOwnDatas($event->component) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $c
            ));
        }
    }
}
