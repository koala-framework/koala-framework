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
    protected function _fireComponentEvent($event, $row, $flag)
    {
        $c = 'Kwf_Component_Event_Component_'.$event;
        foreach ($this->_getDbIdsFromRow($row) as $dbId) {
            $this->fireEvent(new $c($this->_getClassFromRow($row), $dbId, $flag));
        }
    }

    public function onRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        $dc = array_flip($event->row->getDirtyColumns());
        if (isset($dc['visible'])) {
            if ($event->row->visible) {
                $this->_fireComponentEvent('Added', $event->row, Kwf_Component_Event_Component_AbstractFlag::FLAG_VISIBILITY_CHANGED);
            } else {
                $this->_fireComponentEvent('Removed', $event->row, Kwf_Component_Event_Component_AbstractFlag::FLAG_VISIBILITY_CHANGED);
            }
            unset($dc['visible']);
        }
        if (isset($dc['pos']) && $event->row->getModel()->hasColumn('visible') && $event->row->visible) {
            $this->_fireComponentEvent('PositionChanged', $event->row, null);
            unset($dc['pos']);
        }
        if (isset($dc['component']) && $event->row->getModel()->hasColumn('visible') && $event->row->visible) {
            foreach ($this->_getDbIdsFromRow($event->row) as $dbId) {
                foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId) as $c) {
                    $this->fireEvent(new Kwf_Component_Event_Component_RecursiveRemoved($this->_getClassFromRow($event->row, true), $c->componentId));
                    $this->fireEvent(new Kwf_Component_Event_Component_RecursiveAdded($this->_getClassFromRow($event->row, false), $c->componentId));
                }
            }
            unset($dc['component']);
        }
        if (!empty($dc)) {
            foreach ($this->_getDbIdsFromRow($event->row) as $dbId) {
                $this->fireEvent(new Kwf_Component_Event_Component_RowUpdated(
                    $this->_getClassFromRow($event->row), $dbId
                ));
            }
        }
    }

    public function onRowAdd(Kwf_Component_Event_Row_Inserted $event)
    {
        if (!$event->row->getModel()->hasColumn('visible') || $event->row->visible) {
            $this->_fireComponentEvent('Added', $event->row, Kwf_Component_Event_Component_AbstractFlag::FLAG_ROW_ADDED_REMOVED);
        }
    }

    public function onRowDelete(Kwf_Component_Event_Row_Deleted $event)
    {
        if (!$event->row->getModel()->hasColumn('visible') || $event->row->visible) {
            $this->_fireComponentEvent('Removed', $event->row, Kwf_Component_Event_Component_AbstractFlag::FLAG_ROW_ADDED_REMOVED);
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

    //overrridden in Kwc_Root_Category_GeneratorEvents
    protected function _getDbIdsFromRow($row)
    {
        if ($this->_getGenerator()->hasSetting('dbIdShortcut') && $this->_getGenerator()->getSetting('dbIdShortcut')) {
            return array($this->_getGenerator()->getSetting('dbIdShortcut') .
                $row->id);
        } else if ($row->getModel()->hasColumn('component_id')) {
            return array($row->component_id .
                $this->_getGenerator()->getIdSeparator() .
                $row->id);
        } else {
            $cls = $this->_getClassFromRow($row);
            $c = Kwf_Component_Data_Root::getInstance()
                ->getComponentsByClass($cls, array('id' => $this->_getGenerator()->getIdSeparator().$row->id));
            $ret = array();
            foreach ($c as $i) {
                $ret[] = $i->dbId;
            }
            return $ret;
        }
    }
}
