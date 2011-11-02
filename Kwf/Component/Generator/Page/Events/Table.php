<?php
class Kwf_Component_Generator_Page_Events_Table extends Kwf_Component_Generator_PseudoPage_Events_Table
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => $this->_config['componentClass'],
            'event' => 'Kwf_Component_Event_Component_Added',
            'callback' => 'onComponentEvent'
        );
        $ret[] = array(
            'class' => $this->_config['componentClass'],
            'event' => 'Kwf_Component_Event_Component_Removed',
            'callback' => 'onComponentEvent'
        );
        $ret[] = array(
            'class' => $this->_config['componentClass'],
            'event' => 'Kwf_Component_Event_Component_ClassChanged',
            'callback' => 'onComponentEvent'
        );
        $ret[] = array(
            'class' => $this->_config['componentClass'],
            'event' => 'Kwf_Component_Event_Component_PositionChanged',
            'callback' => 'onComponentEvent'
        );
        return $ret;
    }

    public function onComponentEvent(Kwf_Component_Event_Component_Abstract $event)
    {
        $eventsClass = null;
        if ($event instanceof Kwf_Component_Event_Component_Added) {
            $eventsClass = 'Kwf_Component_Event_Page_Added';
        } else if ($event instanceof Kwf_Component_Event_Component_Removed) {
            $eventsClass = 'Kwf_Component_Event_Page_Removed';
        } else if ($event instanceof Kwf_Component_Event_Component_ClassChanged) {
            $eventsClass = 'Kwf_Component_Event_Page_ClassChanged';
        } else if ($event instanceof Kwf_Component_Event_Component_PositionChanged) {
            $eventsClass = 'Kwf_Component_Event_Page_PositionChanged';
        }
        if ($eventsClass) {
            $this->fireEvent(new $eventsClass($this->_class, $event->dbId));
        }
    }
}