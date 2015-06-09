<?php
class Kwf_Component_Events_ProcessInputCache extends Kwf_Events_Subscriber
{
    public function getListeners()
    {
        $ret = array();
        $processInputClasses = array();
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (Kwc_Abstract::getFlag($c, 'processInput')) {
                $processInputClasses[] = $c;
            }
        }
        $ret[] = array(
            'class' => $processInputClasses,
            'event' => 'Kwf_Component_Event_Component_Added',
            'callback' => 'onComponentAddedOrRemoved'
        );
        $ret[] = array(
            'class' => $processInputClasses,
            'event' => 'Kwf_Component_Event_Component_Removed',
            'callback' => 'onComponentAddedOrRemoved'
        );
        return $ret;
    }

    //clear cache used in Kwf_Component_Abstract_ContentSender_Default
    public function onComponentAddedOrRemoved(Kwf_Component_Event_Component_Abstract $event)
    {
        $cacheId = 'procI-'.$event->component->getPageOrRoot()->componentId;
        Kwf_Cache_Simple::delete($cacheId);
        $log = Kwf_Events_Log::getInstance();
        if ($log) {
            $log->log("processInput cache clear componentId=".$event->component->getPageOrRoot()->componentId, Zend_Log::INFO);
        }
    }
}