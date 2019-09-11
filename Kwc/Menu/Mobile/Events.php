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
            'event' => 'Kwf_Component_Event_Page_NameChanged',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_UrlChanged',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Component_RecursiveRemoved',
            'callback' => 'onPageChangedRecursive'
        );
        return $ret;
    }

    public function onPageChanged(Kwf_Events_Event_Abstract $event)
    {
        $this->_deleteCacheByEvent($event);
    }

    public function onPageChangedRecursive(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        $this->_deleteCacheByEvent($event);
    }

    private function _deleteCacheByEvent($event)
    {
        $d = $event->component->getParentPage();
        if (!$d) {
            $d = $event->component->getSubroot();
        }
        while ($d) {
            if ($this->_deleteCache($d)) break;
            $d = $d->parent;
        }
    }

    private function _deleteCache(Kwf_Component_Data $d, $deleteCacheForParent = true)
    {
        if (Kwc_Abstract::getFlag($d->componentClass, 'subroot') || $d->componentId == 'root') {
            Kwf_Cache_Simple::delete('kwcMenuMobile-root-'.$d->componentId.'-'.$this->_class);

            return true;
        } else if ($d->isPage) {
            Kwf_Cache_Simple::delete('kwcMenuMobile-'.$d->componentId);
            if ($deleteCacheForParent) $this->_deleteCache(($p = $d->getParentPage()) ? $p : $d->getSubroot(), false);

            return true;
        }

        return false;
    }
}
