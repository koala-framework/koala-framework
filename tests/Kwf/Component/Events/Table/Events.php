<?php
class Kwf_Component_Events_Table_Events extends Kwf_Component_Abstract_Events
{
    public $countCalled = 0;

    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_Added',
            'callback' => 'onComponentChange'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_Removed',
            'callback' => 'onComponentChange'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_PositionChanged',
            'callback' => 'onComponentChange'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveAdded',
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