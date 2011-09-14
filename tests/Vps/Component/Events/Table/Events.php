<?php
class Vps_Component_Events_Table_Events extends Vps_Component_Abstract_Events
{
    public $countCalled = 0;

    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => 'Vps_Component_Event_Component_Added',
            'callback' => 'onComponentChange'
        );
        $ret[] = array(
            'event' => 'Vps_Component_Event_Component_Removed',
            'callback' => 'onComponentChange'
        );
        $ret[] = array(
            'event' => 'Vps_Component_Event_Component_Moved',
            'callback' => 'onComponentChange'
        );
        $ret[] = array(
            'event' => 'Vps_Component_Event_Component_ClassChanged',
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