<?php
class Kwc_Menu_ParentMenu_Events extends Kwc_Abstract_Events
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

    public function onMenuContentChanged(Kwf_Component_Event_Component_ContentChanged $event)
    {
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($event->dbId) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
                $this->_class, $c->getPageOrRoot()->componentId
            ));
        }
    }

    public function onMenuHasContentChanged(Kwf_Component_Event_Component_HasContentChanged $event)
    {
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($event->dbId) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveHasContentChanged(
                $this->_class, $c->getPageOrRoot()->componentId
            ));
        }
    }
}
