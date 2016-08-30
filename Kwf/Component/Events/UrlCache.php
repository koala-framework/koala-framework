<?php
class Kwf_Component_Events_UrlCache extends Kwf_Events_Subscriber
{
    private $_constraints;

    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'event' => 'Kwf_Events_Event_Row_UpdatesFinished',
            'callback' => 'onRowUpdatesFinished'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_RecursiveUrlChanged',
            'callback' => 'onPageRecursiveUrlChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_UrlChanged',
            'callback' => 'onPageUrlChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_NameChanged',
            'callback' => 'onPageNameChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveRemoved',
            'callback' => 'onComponentRecursiveRemoved'
        );
        return $ret;
    }

    protected function _init()
    {
        parent::_init();
    }

    public function onRowUpdatesFinished(Kwf_Events_Event_Row_UpdatesFinished $event)
    {
        if ($this->_constraints) {
            Kwf_Component_Cache_Url_Abstract::getInstance()->delete($this->_constraints);
            $this->_constraints = array();
        }
    }

    public function onPageUrlChanged(Kwf_Component_Event_Page_UrlChanged $event)
    {
        $c = $event->component->getPageOrRoot();
        $this->_constraints[] = array('field'=>'expanded_page_id', 'value'=>$c->getExpandedComponentId());
    }

    public function onPageNameChanged(Kwf_Component_Event_Page_NameChanged $event)
    {
        $c = $event->component->getPageOrRoot();
        $this->_constraints[] = array('field'=>'page_id', 'value'=>$c->componentId);
    }

    public function onPageRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        $c = $event->component->getPageOrRoot();
        $this->_constraints[] = array('field'=>'expanded_page_id', 'value'=>$c->getExpandedComponentId().'%');
    }

    public function onComponentRecursiveRemoved(Kwf_Component_Event_Component_RecursiveRemoved $event)
    {
        $c = $event->component->getPageOrRoot();
        $this->_constraints[] = array('field'=>'expanded_page_id', 'value'=>$c->getExpandedComponentId().'%');
    }
}
