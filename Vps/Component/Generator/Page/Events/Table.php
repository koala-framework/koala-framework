<?php
class Vps_Component_Generator_Page_Events_Table extends Vps_Component_Generator_Events_Table
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => $this->_config['componentClass'],
            'event' => 'Vps_Component_Event_Component_Added',
            'callback' => 'onComponentEvent'
        );
        return $ret;
    }

    public function onComponentEvent(Vps_Component_Event_Component_Abstract $event)
    {
        $eventsClass = null;
        if ($event instanceof Vps_Component_Event_Component_Added) {
            $eventsClass = 'Vps_Component_Event_Page_Added';
        } else if ($event instanceof Vps_Component_Event_Component_Removed) {
            $eventsClass = 'Vps_Component_Event_Page_Removed';
        } else if ($event instanceof Vps_Component_Event_Component_ClassChanged) {
            $eventsClass = 'Vps_Component_Event_Page_ClassChanged';
        } else if ($event instanceof Vps_Component_Event_Component_Moved) {
            $eventsClass = 'Vps_Component_Event_Page_Moved';
        }
        if ($eventsClass) {
            $this->fireEvent(new $eventsClass($this->_class, $event->dbId));
        }
    }
}