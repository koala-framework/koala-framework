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
        $dc = array_flip($event->row->getDirtyColumns());
        $dbId = $this->_getDbId($event->row);
        $class = $this->_class;
        if (isset($dc['visible'])) {
            if ($event->row->visible) {
                $this->fireEvent(new Vps_Component_Event_Component_Added($class, $dbId));
            } else {
                $this->fireEvent(new Vps_Component_Event_Component_Removed($class, $dbId));
            }
            unset($dc['visible']);
        }
        if (isset($dc['pos']) && $event->row->visible) {
            $this->fireEvent(new Vps_Component_Event_Component_PositionChanged($class, $dbId));
            unset($dc['pos']);
        }
        if (isset($dc['component']) && $event->row->visible) {
            $this->fireEvent(new Vps_Component_Event_Component_ClassChanged($class, $dbId));
            unset($dc['pos']);
        }
        if (!empty($db)) {
            $this->fireEvent(new Vps_Component_Event_Component_ContentChanged($class, $dbId));
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
