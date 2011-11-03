<?php
class Kwf_Component_Generator_Events_Table extends Kwf_Component_Generator_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Kwf_Component_Event_Row_Inserted',
            'callback' => 'onRowAdd'
        );
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Kwf_Component_Event_Row_Deleted',
            'callback' => 'onRowDelete'
        );
        return $ret;
    }

    //overridden in Page_Events_Table to fire Page events
    protected function _fireComponentEvent($event, $row)
    {
        $c = 'Kwf_Component_Event_Component_'.$event;
        $this->fireEvent(new $c($this->_getClassFromRow($row), $this->_getDbIdFromRow($row)));
    }

    public function onRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        $dc = array_flip($event->row->getDirtyColumns());
        if (isset($dc['visible'])) {
            if ($event->row->visible) {
                $this->_fireComponentEvent('Added', $event->row);
            } else {
                $this->_fireComponentEvent('Removed', $event->row);
            }
        }
        if (isset($dc['pos']) && isset($event->row->visible) && $event->row->visible) {
            $this->_fireComponentEvent('PositionChanged', $event->row);
        }
        if (isset($dc['component']) && isset($event->row->visible) && $event->row->visible) {
            $id = $this->_getDbIdFromRow($event->row);
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($id) as $c) {
                $this->fireEvent(new Kwf_Component_Event_Component_RecursiveRemoved($this->_getClassFromRow($event->row, true), $c->componentId));
                $this->fireEvent(new Kwf_Component_Event_Component_RecursiveAdded($this->_getClassFromRow($event->row, false), $c->componentId));
            }
        }
    }

    public function onRowAdd(Kwf_Component_Event_Row_Inserted $event)
    {
        if (!$event->row->hasColumn('visible') || $event->row->visible) {
            $this->_fireComponentEvent('Added', $event->row);
        }
    }

    public function onRowDelete(Kwf_Component_Event_Row_Deleted $event)
    {
        if (!$event->row->hasColumn('visible') || $event->row->visible) {
            $this->_fireComponentEvent('Removed', $event->row);
        }
    }

    protected function _getClassFromRow($row, $cleanValue = false)
    {
        $classes = $this->_getGenerator()->getChildComponentClasses();
        if (count($classes) > 1 && $row->getModel()->hasColumn('component')) {
            if ($cleanValue) {
                $c = $row->getCleanValue('component');
            } else {
                $c = $row->component;
            }
            if (isset($classes[$c])) {
                return $classes[$row->component];
            }
        }
        $class = array_shift($classes);
        return $class;
    }

    protected function _getDbIdFromRow($row)
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
