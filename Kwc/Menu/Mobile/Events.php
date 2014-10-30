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
        $this->_deleteCache($event);
        $page = $event->component->getParentPageOrRoot()->getParentPageOrRoot();
        $d = $page;
        while ($d) {
            if ($d->getFlag('subroot')) {
                Kwf_Cache_Simple::delete('kwcMenuMobile-root-'.$d->componentId);
                break;
            } else if ($d->isPage) {
                Kwf_Cache_Simple::delete('kwcMenuMobile-'.$d->componentId);
                break;
            }
            $d = $d->parent;
        }
    }

    public function onPageChangedRecursive(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        $this->_deleteCache($event);
    }

    private function _deleteCache($event)
    {
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Menu_Mobile_Component', array('subroot' => $event->component->getSubroot()));
        foreach($components as $component) {
            $cacheId = 'kwcMenuMobile-' . $component->componentId;
            Kwf_Cache_Simple::delete($cacheId);
        }
    }
}
