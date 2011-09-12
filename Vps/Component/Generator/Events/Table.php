<?php
class Vps_Component_Generator_Events_Table extends Vps_Component_Generator_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => Vps_Component_Events::EVENT_ROW_UPDATE,
            'callback' => 'onRowUpdate'
        );
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => Vps_Component_Events::EVENT_ROW_INSERT,
            'callback' => 'onRowAdd'
        );
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => Vps_Component_Events::EVENT_ROW_DELETE,
            'callback' => 'onRowDelete'
        );
        return $ret;
    }

    public function onRowUpdate($event, $row)
    {
        if (in_array('visible', $row->getDirtyColumns())) {
            if ($row->visible) {
                $event = Vps_Component_Events::EVENT_COMPONENT_ADD;
            } else {
                $event = Vps_Component_Events::EVENT_COMPONENT_REMOVE;
            }
            $this->fireEvent($event, $this->_config['componentClass'], $row);
        }
        if (in_array('pos', $row->getDirtyColumns()) && $row->visible) {
            $event = Vps_Component_Events::EVENT_COMPONENT_MOVE;
            $this->fireEvent($event, $this->_config['componentClass'], $row);
        }
        if (in_array('component', $row->getDirtyColumns()) && $row->visible) {
            $event = Vps_Component_Events::EVENT_COMPONENT_CLASS_CHANGE;
            $this->fireEvent($event, $this->_config['componentClass'], $row);
        }
    }

    public function onRowAdd($event, $row)
    {
        if ($row->visible) {
            $this->fireEvent(Vps_Component_Events::EVENT_COMPONENT_ADD, $this->_config['componentClass'], $row);
        }
    }

    public function onRowDelete($event, $row)
    {
        if ($row->visible) {
            $this->fireEvent(Vps_Component_Events::EVENT_COMPONENT_REMOVE, $this->_config['componentClass'], $row);
        }
    }
}
