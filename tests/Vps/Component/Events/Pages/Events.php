<?php
class Vps_Component_Events_Pages_Events extends Vps_Component_Abstract_Events
{
    public $countCalled = 0;

    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'event' => Vps_Component_Events::EVENT_PAGE_ADD,
            'callback' => 'onComponentChange'
        );
        $ret[] = array(
            'event' => Vps_Component_Events::EVENT_PAGE_PARENT_CHANGE,
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