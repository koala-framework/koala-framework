<?php
class Kwc_Box_MetaTags_Events extends Kwc_Abstract_Composite_Events
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
}
