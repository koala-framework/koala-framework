<?php
class Vps_Component_Events_Table_Events extends Vps_Component_Abstract_Events
{
    public $countCalled = 0;

    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => Vps_Component_Events::EVENT_COMPONENT_ADD,
            'callback' => 'onComponentChange'
        );
        $ret[] = array(
            'event' => Vps_Component_Events::EVENT_COMPONENT_REMOVE,
            'callback' => 'onComponentChange'
        );
        $ret[] = array(
            'event' => Vps_Component_Events::EVENT_COMPONENT_MOVE,
            'callback' => 'onComponentChange'
        );
        $ret[] = array(
            'event' => Vps_Component_Events::EVENT_COMPONENT_CLASS_CHANGE,
            'callback' => 'onComponentChange'
        );
        return $ret;
    }

    public function onComponentChange($event, $row)
    {
        $this->countCalled++;
    }
}
?>