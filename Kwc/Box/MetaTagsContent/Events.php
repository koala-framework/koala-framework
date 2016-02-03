<?php
class Kwc_Box_MetaTagsContent_Events extends Kwc_Box_MetaTags_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_UrlChanged',
            'callback' => 'onUrlChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_RecursiveUrlChanged',
            'callback' => 'onRecursiveUrlChanged'
        );
        return $ret;
    }

    public function onUrlChanged(Kwf_Component_Event_Page_UrlChanged $event)
    {
        foreach ($event->component->getRecursiveChildComponents(array('componentClass' => $this->_class)) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                $this->_class, $c
            ));
        }
    }

    public function onRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        foreach ($event->component->getRecursiveChildComponents(array('componentClass' => $this->_class)) as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
                $this->_class, $c
            ));
        }
    }

    protected function _onOwnRowUpdate(Kwf_Component_Data $c, Kwf_Events_Event_Row_Abstract $event)
    {
        parent::_onOwnRowUpdate($c, $event);
        if (Kwc_Abstract::getFlag($c->parent->componentClass, 'subroot') || $c->parent instanceof Kwf_Component_Data_Root) {
            $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
                $this->_class, $c
            ));
        }
    }
}
