<?php
class Kwc_Chained_Trl_GeneratorEvents_Table extends Kwc_Chained_Trl_GeneratorEvents_Abstract
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $masterGeneratorModel = $this->_getChainedGenerator()->getModel();
        $ret[] = array(
            'class' => get_class($masterGeneratorModel),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onMasterRowUpdate'
        );
        $ret[] = array(
            'class' => get_class($masterGeneratorModel),
            'event' => 'Kwf_Component_Event_Row_Inserted',
            'callback' => 'onMasterRowAdd'
        );
        $ret[] = array(
            'class' => get_class($masterGeneratorModel),
            'event' => 'Kwf_Component_Event_Row_Deleted',
            'callback' => 'onMasterRowDelete'
        );
        $ret[] = array(
            'class' => get_class($masterGeneratorModel),
            'event' => 'Kwf_Component_Event_Model_Updated',
            'callback' => 'onMasterModelUpdate'
        );
        $m = Kwc_Abstract::createChildModel($this->_class);
        if ($m) {
            if ($m->getPrimaryKey() != 'component_id') {
                throw new Kwf_Exception("chained generator model: primary key is not component_id ({$m->getPrimaryKey()}) for {$this->_class}  {$this->_getGenerator()->getGeneratorKey()}");
            }
            $ret[] = array(
                'class' => get_class($m),
                'event' => 'Kwf_Component_Event_Row_Updated',
                'callback' => 'onRowUpdate'
            );
        }
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
        $dbId = $event->row->component_id;

        $dc = array_flip($event->row->getDirtyColumns());

        if (isset($dc['visible'])) {
            if ($event->row->visible) {
                foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId, array('ignoreVisible'=>true)) as $c) {
                    if ($c->generator === $this->_getGenerator()) {
                        $this->_fireComponentEvent('Added', $c, Kwf_Component_Event_Component_AbstractFlag::FLAG_VISIBILITY_CHANGED);
                    }
                }
            } else {
                foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId, array('ignoreVisible'=>true)) as $c) {
                    if ($c->generator === $this->_getGenerator()) {
                        $this->_fireComponentEvent('Removed', $c, Kwf_Component_Event_Component_AbstractFlag::FLAG_VISIBILITY_CHANGED);
                        $this->fireEvent(new Kwf_Component_Event_Component_RecursiveRemoved($c->componentClass, $c));
                    }
                }
            }

            //visible change in master row doesn't effect us
            unset($dc['visible']);
        }

        if (!empty($dc)) {
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId) as $c) {
                if ($c->generator === $this->_getGenerator()) {
                    $this->fireEvent(new Kwf_Component_Event_Component_RowUpdated(
                        $c->componentClass, $c
                    ));
                }
            }
        }
    }

    public function onMasterRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        $dc = array_flip($event->row->getDirtyColumns());
        if (isset($dc['visible'])) {
            //visible change in master row doesn't effect us
            unset($dc['visible']);
        }
        if (isset($dc['pos'])) {
            foreach ($this->_getComponentsFromMasterRow($event->row, array('ignoreVisible'=>false)) as $c) {
                $this->_fireComponentEvent('PositionChanged', $c, null);
            }
            unset($dc['pos']);
        }
        if (isset($dc['component'])) {
            $classes = $this->_getGenerator()->getChildComponentClasses();
            foreach ($this->_getComponentsFromMasterRow($event->row, array('ignoreVisible'=>false)) as $c) {
                $this->fireEvent(new Kwf_Component_Event_Component_RecursiveRemoved($classes[$event->row->component], $c));
                $this->fireEvent(new Kwf_Component_Event_Component_RecursiveAdded($classes[$event->row->component], $c));
            }
            unset($dc['component']);
        }
        if (!empty($dc)) {
            foreach ($this->_getComponentsFromMasterRow($event->row, array('ignoreVisible'=>false)) as $c) {
                $this->fireEvent(new Kwf_Component_Event_Component_RowUpdated(
                    $c->componentClass, $c
                ));
            }
        }
    }

    public function onMasterRowAdd(Kwf_Component_Event_Row_Inserted $event)
    {
        foreach ($this->_getComponentsFromMasterRow($event->row, array('ignoreVisible'=>false)) as $c) {
            $this->_fireComponentEvent('Added', $c, Kwf_Component_Event_Component_AbstractFlag::FLAG_ROW_ADDED_REMOVED);
        }
    }

    public function onMasterRowDelete(Kwf_Component_Event_Row_Deleted $event)
    {
        foreach ($this->_getComponentsFromMasterRow($event->row, array('ignoreVisible'=>false)) as $c) {
            $this->_fireComponentEvent('Removed', $c, Kwf_Component_Event_Component_AbstractFlag::FLAG_ROW_ADDED_REMOVED);
        }
    }

    public function onMasterModelUpdate(Kwf_Component_Event_Model_Updated $event)
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

    protected function _getMasterClassFromMasterRow($row, $cleanValue = false)
    {
        $classes = $this->_getChainedGenerator()->getChildComponentClasses();
        return $this->_getClassFromRow($classes, $row, $cleanValue);
    }

    private function _getMasterComponentsFromMasterRow($row, $select)
    {
        if ($this->_getChainedGenerator()->hasSetting('dbIdShortcut') && $this->_getChainedGenerator()->getSetting('dbIdShortcut')) {
            $dbId = $this->_getChainedGenerator()->getSetting('dbIdShortcut') .
                $row->id;
            return Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId, $select);
        } else if ($row->getModel()->hasColumn('component_id')) {
            $dbId = $row->component_id .
                $this->_getChainedGenerator()->getIdSeparator() .
                $row->id;
            return Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId, $select);
        } else {
            $cls = $this->_getMasterClassFromMasterRow($row);
            $select['id'] = $this->_getChainedGenerator()->getIdSeparator().$row->id;
            return Kwf_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($cls, $select);  // ignoreVisible is necessary to be able to fire Removed events when visibile got false
        }
    }

    protected function _getComponentsFromMasterRow($row, $select)
    {
        $chainedType = 'Trl';

        $ret = array();
        foreach ($this->_getMasterComponentsFromMasterRow($row, array('ignoreVisible'=>true)) as $c) {
            $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($c, $chainedType, $select);
            foreach ($chained as $i) {
                if ($i->generator !== $this->_getGenerator()) {
                    //can happen if two components use same model
                    continue;
                }
            }
            $ret = array_merge($ret, $chained);
        }
        return $ret;
    }
}
