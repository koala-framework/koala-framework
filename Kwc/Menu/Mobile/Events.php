<?php
class Kwc_Menu_Mobile_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_Added',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_Removed',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_PositionChanged',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_ShowInMenuChanged',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_ParentChanged',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Component_RecursiveRemoved',
            'callback' => 'onPageChangedRecursive'
        );
        return $ret;
    }

    public function onPageChanged(Kwf_Component_Event_Abstract $event)
    {
        $this->_deleteCache($event);
    }

    public function onPageChangedRecursive(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        $this->_deleteCache($event);
    }

    private function _deleteCache($event)
    {
        $cacheId = 'menuMobile' . $event->component->getSubroot()->componentId;
        Kwf_Cache_Simple::delete($cacheId);
    }
}
