<?php
class Kwc_Menu_OtherCategory_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'menuComponentClass'),
            'event' => 'Kwf_Component_Event_ComponentClass_ContentChanged',
            'callback' => 'onMenuComponentClassContentChanged'
        );
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'menuComponentClass'),
            'event' => 'Kwf_Component_Event_Component_ContentChanged',
            'callback' => 'onMenuContentChanged'
        );
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'menuComponentClass'),
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

    private function _getOwnDatas($menuDbId)
    {
        $ret = array();
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($menuDbId) as $menu) {
            if (!$menu->parent->parent) continue;
            foreach ($menu->parent->parent->getChildComponents(array('flags' => array('menuCategory'=>true))) as $c) {
                $ret = array_merge($ret, $c->getChildComponents(array('componentClass' => $this->_class)));
            }
        }
        return $ret;
    }

    public function onMenuContentChanged(Kwf_Component_Event_Component_ContentChanged $event)
    {
        foreach ($this->_getOwnDatas($event->dbId) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $c->dbId
            ));
        }
    }

    public function onMenuHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        foreach ($this->_getOwnDatas($event->dbId) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                $this->_class, $c->dbId
            ));
        }
    }
}
