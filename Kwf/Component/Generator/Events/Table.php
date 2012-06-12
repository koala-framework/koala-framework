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
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Kwf_Component_Event_Model_Updated',
            'callback' => 'onModelUpdate'
        );
        return $ret;
    }

    //overridden in Page_Events_Table to fire Page events
    protected function _fireComponentEvent($eventType, Kwf_Component_Data $c, $flag)
    {
        $cls = 'Kwf_Component_Event_Component_'.$eventType;
        $this->fireEvent(new $cls($c->componentClass, $c, $flag));
    }

    public function onRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        $affected = false;
        foreach ($this->_getComponentsFromRow($event->row) as $c) {
            if ($c->generator->getClass() == $this->_class) $affected = true;
        }
        if (!$affected) return;

        $dc = array_flip($event->row->getDirtyColumns());
        if (isset($dc['visible'])) {
            if ($event->row->visible) {
                foreach ($this->_getComponentsFromRow($event->row) as $c) {
                    $this->_fireComponentEvent('Added', $c, Kwf_Component_Event_Component_AbstractFlag::FLAG_VISIBILITY_CHANGED);
                }
            } else {
                foreach ($this->_getComponentsFromRow($event->row) as $c) {
                    $this->_fireComponentEvent('Removed', $c, Kwf_Component_Event_Component_AbstractFlag::FLAG_VISIBILITY_CHANGED);
                    $this->fireEvent(new Kwf_Component_Event_Component_RecursiveRemoved($c->componentClass, $c));
                }
            }
            unset($dc['visible']);
        }
        if (isset($dc['pos']) && $event->row->getModel()->hasColumn('visible') && $event->row->visible) {
            foreach ($this->_getComponentsFromRow($event->row) as $c) {
                $this->_fireComponentEvent('PositionChanged', $c, null);
            }
            unset($dc['pos']);
        }
        if (isset($dc['component']) && $event->row->getModel()->hasColumn('visible') && $event->row->visible) {
            foreach ($this->_getComponentsFromRow($event->row) as $c) {
                $this->fireEvent(new Kwf_Component_Event_Component_RecursiveRemoved($this->_getClassFromRow($event->row, true), $c));
                $this->fireEvent(new Kwf_Component_Event_Component_RecursiveAdded($this->_getClassFromRow($event->row, false), $c));
            }
            unset($dc['component']);
        }
        if (!empty($dc)) {
            foreach ($this->_getComponentsFromRow($event->row) as $c) {
                $this->fireEvent(new Kwf_Component_Event_Component_RowUpdated(
                    $c->componentClass, $c
                ));
            }
        }
    }

    public function onRowAdd(Kwf_Component_Event_Row_Inserted $event)
    {
        if (!$event->row->getModel()->hasColumn('visible') || $event->row->visible) {
            foreach ($this->_getComponentsFromRow($event->row) as $c) {
                if ($c->generator && $c->generator->getClass() == $this->_class) {
                    $this->_fireComponentEvent('Added', $c, Kwf_Component_Event_Component_AbstractFlag::FLAG_ROW_ADDED_REMOVED);
                }
            }
        }
    }

    public function onRowDelete(Kwf_Component_Event_Row_Deleted $event)
    {
        if (!$event->row->getModel()->hasColumn('visible') || $event->row->visible) {
            foreach ($this->_getComponentsFromRow($event->row) as $c) {
                if ($c->generator->getClass() == $this->_class) {
                    $this->_fireComponentEvent('Removed', $c, Kwf_Component_Event_Component_AbstractFlag::FLAG_ROW_ADDED_REMOVED);
                }
            }
        }
    }

    public function onModelUpdate(Kwf_Component_Event_Model_Updated $event)
    {
        //now it's getting inefficient (but this event will /usually/ not be  called at all)

        $classes = $this->_getGenerator()->getChildComponentClasses();
        //we don't know the class, fire event for all possible ones
        foreach ($classes as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_ModelUpdated(
                $c
            ));
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
    protected function _getComponentsFromRow($row)
    {
        if ($this->_getGenerator()->hasSetting('dbIdShortcut') && $this->_getGenerator()->getSetting('dbIdShortcut')) {
            $dbId = $this->_getGenerator()->getSetting('dbIdShortcut') .
                $row->id;
            return Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId, array('ignoreVisible'=>true));
        } else if ($row->getModel()->hasColumn('component_id')) {
            $dbId = $row->component_id .
                $this->_getGenerator()->getIdSeparator() .
                $row->id;
            return Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId, array('ignoreVisible'=>true));
        } else {
            $cls = $this->_getClassFromRow($row);
            return Kwf_Component_Data_Root::getInstance()
                ->getComponentsByClass($cls, array('id' => $this->_getGenerator()->getIdSeparator().$row->id, 'ignoreVisible'=>true));  // ignoreVisible is necessary to be able to fire Removed events when visibile got false
        }
    }
}
