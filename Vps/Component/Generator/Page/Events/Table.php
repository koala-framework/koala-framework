<?php
class Vps_Component_Generator_Page_Events_Table extends Vps_Component_Generator_Events_Table
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => $this->_config['componentClass'],
            'event' => Vps_Component_Events::EVENT_COMPONENT_ADD,
            'callback' => 'onComponentEvent'
        );
        return $ret;
    }

    public function onComponentEvent($event, $row)
    {
        if ($event == Vps_Component_Events::EVENT_COMPONENT_ADD) {
            $event = Vps_Component_Events::EVENT_PAGE_ADD;
        } else if ($event == Vps_Component_Events::EVENT_COMPONENT_REMOVE) {
            $event = Vps_Component_Events::EVENT_PAGE_REMOVE;
        } else if ($event == Vps_Component_Events::EVENT_COMPONENT_CLASS_CHANGE) {
            $event = Vps_Component_Events::EVENT_PAGE_CLASS_CHANGE;
        } else if ($event == Vps_Component_Events::EVENT_COMPONENT_MOVE) {
            $event = Vps_Component_Events::EVENT_PAGE_MOVE;
        } else {
            $event = null;
        }
        if ($event) {
            $this->fireEvent($event, $this->_config['componentClass'], $row);
        }
    }
}