<?php
class Vps_Component_Generator_Events_Table extends Vps_Component_Generator_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Vps_Component_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Vps_Component_Event_Row_Inserted',
            'callback' => 'onRowAdd'
        );
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Vps_Component_Event_Row_Deleted',
            'callback' => 'onRowDelete'
        );
        return $ret;
    }

    public function onRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        if (in_array('visible', $event->row->getDirtyColumns())) {
            if ($event->row->visible) {
                $this->fireEvent(
                    new Vps_Component_Event_Component_Added($this->_class, $this->_getDbId($event->row))
                );
            } else {
                $this->fireEvent(
                    new Vps_Component_Event_Component_Removed($this->_class, $this->_getDbId($event->row))
                );
            }
        }
        if (in_array('pos', $event->row->getDirtyColumns()) && $event->row->visible) {
            $this->fireEvent(
                new Vps_Component_Event_Component_Moved($this->_class, $this->_getDbId($event->row))
            );
        }
        if (in_array('component', $event->row->getDirtyColumns()) && $event->row->visible) {
            $this->fireEvent(
                new Vps_Component_Event_Component_ClassChanged($this->_class, $this->_getDbId($event->row))
            );
        }
    }

    public function onRowAdd(Vps_Component_Event_Row_Inserted $event)
    {
        if ($event->row->visible) {
            $this->fireEvent(
                new Vps_Component_Event_Component_Added($this->_class, $this->_getDbId($event->row))
            );
        }
    }

    public function onRowDelete(Vps_Component_Event_Row_Deleted $event)
    {
        if ($event->row->visible) {
            $this->fireEvent(
                new Vps_Component_Event_Component_Removed($this->_class, $this->_getDbId($event->row))
            );
        }
    }

    protected function _getDbId($row)
    {
        if ($row->hasColumn('component_id')) {
            return $row->component_id .
                $this->_getGenerator()->getIdSeparator() .
                $row->id;
        } else {
            return $row->id;
        }
    }
}
