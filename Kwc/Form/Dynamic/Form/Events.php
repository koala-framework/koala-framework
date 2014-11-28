<?php
class Kwc_Form_Dynamic_Form_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwc_Form_Dynamic_Component',
            'event' => 'Kwf_Component_Event_Component_ContentChanged',
            'callback' => 'onContentChange'
        );
        return $ret;
    }

    public function onContentChange(Kwf_Component_Event_Abstract $event)
    {
        $c = $event->component->getChildComponent('-form');
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $c));
    }
}
