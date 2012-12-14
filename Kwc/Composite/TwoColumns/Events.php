<?php
class Kwc_Composite_TwoColumns_Events extends Kwc_Abstract_Composite_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_ContentWidthChanged',
            'callback' => 'onContentWidthChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveContentWidthChanged',
            'callback' => 'onRecursiveContentWidthChanged'
        );
        return $ret;
    }

    public function onContentWidthChanged(Kwf_Component_Event_Component_ContentWidthChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClassPage_ContentChanged(
            $this->_class, $event->component->getPageOrRoot()
        ));
    }

    public function onRecursiveContentWidthChanged(Kwf_Component_Event_Component_RecursiveContentWidthChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveContentChanged(
            $this->_class, $event->component
        ));
    }
}
