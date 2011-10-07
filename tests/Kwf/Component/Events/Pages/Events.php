<?php
class Vps_Component_Events_Pages_Events extends Vps_Component_Abstract_Events
{
    public $countCalled = 0;

    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => 'Vps_Component_Event_Page_Added',
            'callback' => 'onComponentChange'
        );
        $ret[] = array(
            'event' => 'Vps_Component_Event_Page_ParentChanged',
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