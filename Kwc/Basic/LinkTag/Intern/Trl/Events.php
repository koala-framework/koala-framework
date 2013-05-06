<?php
class Kwc_Basic_LinkTag_Intern_Trl_Events extends Kwc_Chained_Trl_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
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

    public function onPageRemovedAdded(Kwf_Component_Event_Component_AbstractFlag $event)
    {
        if (!isset($event->component->chained)) return;

        $masterDatas = Kwc_Basic_LinkTag_Intern_Events::getComponentsForTarget(
            Kwc_Abstract::getSetting($this->_class, 'masterComponentClass'),
            $event->component->chained->dbId,
            false
        );
        foreach ($masterDatas as $c) {
            $c = Kwc_Chained_Trl_Component::getChainedByMaster($c, $event->component);
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $c));
            if ($c->isPage) {
                $this->fireEvent(new Kwf_Component_Event_Page_UrlChanged($this->_class, $c));
            }
        }
    }
}
