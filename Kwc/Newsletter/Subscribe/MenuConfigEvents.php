<?php
class Kwc_Newsletter_Subscribe_MenuConfigEvents extends Kwf_Component_Abstract_MenuConfig_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (is_instance_of($c, 'Kwc_Newsletter_Component')) {
                $ret[] = array(
                    'class' => $c,
                    'event' => 'Kwf_Component_Event_Component_Added',
                    'callback' => 'onComponentAddedRemoved'
                );
                $ret[] = array(
                    'class' => $c,
                    'event' => 'Kwf_Component_Event_Component_Removed',
                    'callback' => 'onComponentAddedRemoved'
                );
                $ret[] = array(
                    'class' => $c,
                    'event' => 'Kwf_Component_Event_Component_InvisibleAdded',
                    'callback' => 'onComponentAddedRemoved'
                );
                $ret[] = array(
                    'class' => $c,
                    'event' => 'Kwf_Component_Event_Component_InvisibleRemoved',
                    'callback' => 'onComponentAddedRemoved'
                );
            }
        }
        return $ret;
    }

    public function onComponentAddedRemoved(Kwf_Component_Event_Component_Abstract $ev)
    {
        Kwf_Acl::clearCache();
    }
}
