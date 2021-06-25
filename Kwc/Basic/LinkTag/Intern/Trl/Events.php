<?php
class Kwc_Basic_LinkTag_Intern_Trl_Events extends Kwc_Chained_Trl_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_RecursiveUrlChanged',
            'callback' => 'onRecursiveUrlChanged'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Component_RecursiveRemoved',
            'callback' => 'onRecursiveRemovedAdded'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Component_RecursiveAdded',
            'callback' => 'onRecursiveRemovedAdded'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_Added',
            'callback' => 'onPageRemovedAdded'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_Removed',
            'callback' => 'onPageRemovedAdded'
        );
        return $ret;
    }

    //usually child componets can be deleted using %, but not those from pages table as the ids always start with numeric
    //this method returns all child ids needed for deleting recursively
    private function _getIdsFromRecursiveEvent(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        $c = $event->component->chained;
        $ids = array($c->dbId);
        $c = $c->getPageOrRoot();
        foreach (Kwf_Component_Data_Root::getInstance()->getPageGenerators() as $gen) {
            foreach ($gen->getRecursivePageChildIds($c->dbId) as $id) { //similar to master, but also invisible ones
                $ids[] = (string)$id;
            }
        }
        return $ids;
    }

    public function onRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        if (!isset($event->component->chained)) return;

        $childPageIds = $this->_getIdsFromRecursiveEvent($event);

        $masterDatas = Kwc_Basic_LinkTag_Intern_Events::getComponentsForTarget(
            Kwc_Abstract::getSetting($this->_class, 'masterComponentClass'),
            $childPageIds,
            true,
            $event->component->getSubroot()
        );
        foreach ($masterDatas as $c) {
            $c = Kwc_Chained_Trl_Component::getChainedByMaster($c, $event->component);
            if ($c) {
                $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $c));
                if ($c->isPage) {
                    $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged($this->_class, $c));
                }
            }
        }
    }

    public function onRecursiveRemovedAdded(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        if (!isset($event->component->chained)) return;

        $childPageIds = $this->_getIdsFromRecursiveEvent($event);

        $masterDatas = Kwc_Basic_LinkTag_Intern_Events::getComponentsForTarget(
            Kwc_Abstract::getSetting($this->_class, 'masterComponentClass'),
            $childPageIds,
            true,
            $event->component->getSubroot()
        );
        foreach ($masterDatas as $c) {
            $c = Kwc_Chained_Trl_Component::getChainedByMaster($c, $event->component);
            if ($c) {
                $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $c));
                if ($c->isPage) {
                    $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged($this->_class, $c));
                }
            }
        }

    }

    public function onPageRemovedAdded(Kwf_Component_Event_Component_AbstractFlag $event)
    {
        if (!isset($event->component->chained)) return;

        $masterDatas = Kwc_Basic_LinkTag_Intern_Events::getComponentsForTarget(
            Kwc_Abstract::getSetting($this->_class, 'masterComponentClass'),
            array((string)$event->component->chained->dbId),
            false,
            $event->component->getSubroot()
        );
        foreach ($masterDatas as $c) {
            $c = Kwc_Chained_Trl_Component::getChainedByMaster($c, $event->component);
            if ($c) {
                $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $c));
                if ($c->isPage) {
                    $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged($this->_class, $c));
                }
            }
        }
    }
}
