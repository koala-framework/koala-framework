<?php
class Kwf_Component_Abstract_MenuConfig_SameClass_Events extends Kwf_Component_Abstract_MenuConfig_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => $this->_class,
            'event' => 'Kwf_Component_Event_Component_Added',
            'callback' => 'onComponentAddedRemoved'
        );
        $ret[] = array(
            'class' => $this->_class,
            'event' => 'Kwf_Component_Event_Component_Removed',
            'callback' => 'onComponentAddedRemoved'
        );
        $ret[] = array(
            'class' => $this->_class,
            'event' => 'Kwf_Component_Event_Component_InvisibleAdded',
            'callback' => 'onComponentAddedRemoved'
        );
        $ret[] = array(
            'class' => $this->_class,
            'event' => 'Kwf_Component_Event_Component_InvisibleRemoved',
            'callback' => 'onComponentAddedRemoved'
        );
        return $ret;
    }

    public function onComponentAddedRemoved(Kwf_Component_Event_Component_Abstract $ev)
    {
        Kwf_Acl::clearCache();
    }
}
