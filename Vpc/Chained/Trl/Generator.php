<?php
class Vpc_Chained_Trl_Generator extends Vps_Component_Generator_Abstract
{
    private $_rows = array();

    protected function _init()
    {
        parent::_init();
        $this->_inherits = $this->_getChainedGenerator()->getInherits();
    }

    protected function _getRows($ids)
    {
        $idsToLoad = array();
        foreach ($ids as $i) {
            if (!array_key_exists($i, $this->_rows)) {
                $idsToLoad[] = $i;
            }
        }
        if ($idsToLoad) {
            //nicht $this->getModel, das ist das master model
            $m = Vpc_Abstract::createChildModel($this->_class);
            if (!$m) throw new Vps_Exception("No child model set for '$this->_class'");
            $s = $m->select();
            $s->whereEquals('component_id', $idsToLoad);
            $visible = array();
            Vps_Benchmark::count('DirectoryTrlGenerator getRows', count($idsToLoad));
            foreach ($m->getRows($s) as $r) {
                $this->_rows[$r->component_id] = $r;
            }
            foreach ($idsToLoad as $i) {
                if (!array_key_exists($i, $this->_rows)) {
                    $this->_rows[$i] = null;
                }
            }
        }
        $ret = array();
        foreach ($ids as $i) {
            $ret[$i] = $this->_rows[$i];
        }
        return $ret;
    }

    protected function _getRow($id)
    {
        $r = $this->_getRows(array($id));
        return $r[$id];
    }

    public function getPagesControllerConfig($component)
    {
        $ret = $this->_getChainedGenerator()->getPagesControllerConfig($component, $this->getClass());
        $ret['allowDrag'] = false;
        $ret['allowDrop'] = false;
        $ret['iconEffects'][] = 'chained';
        return $ret;
    }

    protected function _getChainedData($data)
    {
        // TODO: Das ist nicht wirklich korrekt, reicht aber bis jetzt aus
        /*
         * Wenn man eine MasterAsChild hat und Boxen vererbt werden,
         * haben diese Boxen kein chained gesetzt, brauchen es uU.
         * aber. Daher wird nach oben gesucht und die erste chained
         * zurückgegeben, eigentlich sollte aber wieder reingesucht
         * werden, damit es korrekt ist.
         */
        while ($data) {
            if (isset($data->chained)) return $data->chained;
            $data = $data->parent;
        }
        return null;
    }

    protected function _getChainedChildComponents($parentData, $select)
    {
        $select = clone $select;
        if ($p = $select->getPart(Vps_Component_Select::WHERE_CHILD_OF_SAME_PAGE)) {
            $select->whereChildOfSamePage($this->_getChainedData($p));
        }
        if ($cls = $select->getPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES)) {
            foreach ($cls as &$c) {
                $c = substr($c, strpos($c, '.')+1);
            }
            $select->whereComponentClasses($cls);
        }
        if ($sr = $select->getPart(Vps_Component_Select::WHERE_SUBROOT)) {
            $newSr = array();
            foreach ($sr as $i) {
                if (isset($i->chained)) {
                    $newSr[] = $i->chained;
                } else {
                    $newSr[] = $i;
                }
            }
            $select->setPart(Vps_Component_Select::WHERE_SUBROOT, $newSr);
        }

        $select->ignoreVisible();

        return $this->_getChainedGenerator()
            ->getChildData($this->_getChainedData($parentData), $select);
    }

    public function getChildData($parentDatas, $select = array())
    {
        $ret = array();
        if (is_array($select)) $select = new Vps_Component_Select($select);
        if ($id = $select->getPart(Vps_Component_Select::WHERE_ID)) {
            if ($this->_getChainedGenerator() instanceof Vpc_Root_Category_Generator) {
                $select->whereId(substr($id, 1));
            }
        }
        $slaveData = $select->getPart(Vps_Component_Select::WHERE_CHILD_OF_SAME_PAGE);
        $parentDataSelect = new Vps_Component_Select();
        $parentDataSelect->copyParts(array('ignoreVisible'), $select);

        $parentDatas = is_array($parentDatas) ? $parentDatas : array($parentDatas);
        foreach ($parentDatas as $parentData) {
            foreach ($this->_getChainedChildComponents($parentData, $select) as $component) {

                $pData = array();
                if (!$parentData) {
                    if (!$slaveData) {
                        foreach (Vps_Component_Data_Root::getInstance()->getComponentsByClass('Vpc_Root_TrlRoot_Chained_Component') as $d) {
                            $chainedComponent = Vpc_Chained_Trl_Component::getChainedByMaster($component->parent, $d, $parentDataSelect);
                            if ($chainedComponent) $pData[] = $chainedComponent;
                        }
                    } else {
                        $chainedComponent = Vpc_Chained_Trl_Component::getChainedByMaster($component->parent, $slaveData, $parentDataSelect);
                        if ($chainedComponent) $pData = array($chainedComponent);
                    }
                } else {
                    $pData = array($parentData);
                }
                foreach ($pData as $d) {
                    $data = $this->_createData($d, $component, $select);
                    if ($data) {
                        $ret[] = $data;
                    }
                }
            }
        }
        return $ret;
    }

    protected function _getIdFromRow($row)
    {
        if (is_numeric($row->componentId)) return $row->componentId;
        return substr($row->componentId, max(strrpos($row->componentId, '-'),strrpos($row->componentId, '_'))+1);
    }

    protected function _formatConfig($parentData, $row)
    {
        $componentClass = $this->_settings['component'][$this->_settings['masterComponentsMap'][$row->componentClass]];
        $id = $this->_getIdFromRow($row);
        $data = array(
            'componentId' => $parentData->componentId.$this->getIdSeparator().$id,
            'dbId' => $parentData->dbId.$this->getIdSeparator().$id,
            'componentClass' => $componentClass,
            'parent' => $parentData,
            'chained' => $row,
            'isPage' => $row->isPage,
            'isPseudoPage' => $row->isPseudoPage,
        );
        if (isset($row->filename)) {
            $data['filename'] = $row->filename;
        }
        if (isset($row->name)) {
            $data['name'] = $row->name;
        }
        if (isset($row->box)) {
            $data['box'] = $row->box;
        }
        if (isset($row->row)) {
            $data['row'] = $row->row;
        }
        return $data;
    }

    public function getChildIds($parentData, $select = array())
    {
        return $this->_getChainedGenerator()
            ->getChildIds($this->_getChainedData($parentData), $select);
    }

    protected function _getChainedGenerator()
    {
        return Vps_Component_Generator_Abstract
            ::getInstance(Vpc_Abstract::getSetting($this->_class, 'masterComponentClass'), $this->_settings['generator']);
    }

    public function getIdSeparator()
    {
        $ret = $this->_getChainedGenerator()->getIdSeparator();
        if (!$ret) $ret = '_'; //pages generator
        return $ret;
    }

    public function getPriority()
    {
        return $this->_getChainedGenerator()->getPriority();
    }

    public function getBoxes()
    {
        return $this->_getChainedGenerator()->getBoxes();
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $flags = $this->_getChainedGenerator()->getGeneratorFlags();

        $copyFlags = array('pageGenerator', 'showInPageTreeAdmin', 'page', 'pseudoPage', 'box', 'multiBox', 'table', 'static', 'hasHome');
        foreach ($copyFlags as $f) {
            if (isset($flags[$f])) {
                $ret[$f] = $flags[$f];
            }
        }

        if (is_instance_of($this->_class, 'Vpc_Root_TrlRoot_Chained_Component')) {
            $ret['trlBase'] = true;
        }
        return $ret;
    }


    public function getModel()
    {
        return $this->_getChainedGenerator()->getModel();
    }

    public function getCacheVars($parentData)
    {
        return $this->_getChainedGenerator()->getCacheVars($parentData->chained);
    }

    public function makeChildrenVisible($source)
    {
        if ($source->generator !== $this) {
            throw new Vps_Exception("you must call this only with the correct source");
        }

        $m = Vpc_Abstract::createChildModel($this->_class);
        if ($m && $m->hasColumn('visible')) {
            $row = $this->_getRow($source->dbId);
            if (!$row) {
                $row = $m->createRow();
                $row->component_id = $source->dbId;
            }
            if (!$row->visible) {
                $row->visible = 1;
                $row->save();
            }
        }
        Vpc_Admin::getInstance($source->componentClass)->makeVisible($source);
    }

    public function duplicateChild($source, $parentTarget)
    {
        if ($source->generator !== $this) {
            throw new Vps_Exception("you must call this only with the correct source");
        }

        //Annahme: sourceChildren und targetChildren müssen in der gleichen Reinhenfolge daherkommen
        //gibt es einen generator ohne pos oder datum oder ähnlichem?
        $sourceChildren = array_values($source->parent->getChildComponents(array('ignoreVisible'=>true, 'generator'=>$this->getGeneratorKey())));
        $targetChildren = array_values($parentTarget->getChildComponents(array('ignoreVisible'=>true, 'generator'=>$this->getGeneratorKey())));
        $target = null;
        foreach ($sourceChildren as $i=>$sc) {
            if ($sc->componentId == $source->componentId) {
                $target = $targetChildren[$i];
            }
        }
        if ($m = Vpc_Abstract::createChildModel($this->_class)) {
            $targetRow = $this->_getRow($target->dbId);
            if (!$targetRow) {
                $targetRow = $m->createRow();
                $targetRow->component_id = $target->dbId;
            }
            $sourceRow = $this->_getRow($source->dbId);
            if ($sourceRow) {
                foreach ($sourceRow->toArray() as $k=>$i) {
                    if ($this->_getRow($source->dbId)->getModel()->getPrimaryKey() != $k) {
                        $targetRow->$k = $i;
                    }
                }
            }
            $targetRow->save();
        }
        Vpc_Admin::getInstance($source->componentClass)->duplicate($source, $target);
        return $target;
    }
}
