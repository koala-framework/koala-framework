<?php
class Kwf_Component_Events_PseudoPage_Events extends Kwf_Component_Abstract_Events
{
    public $countCalled = 0;

    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_NameChanged',
            'callback' => 'onComponentChange'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_RecursiveUrlChanged',
            'callback' => 'onComponentChange'
        );
        return $ret;
    }

    public function onComponentChange($event)
    {
        $this->countCalled++;
    }
}
?>