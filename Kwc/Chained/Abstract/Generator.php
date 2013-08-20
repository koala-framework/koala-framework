<?php
class Kwc_Chained_Abstract_Generator extends Kwf_Component_Generator_Abstract
{
    private $_rows = array();

    protected function _init()
    {
        parent::_init();
        $this->_inherits = $this->_getChainedGenerator()->getInherits();
        $this->_addUrlPart = $this->_getChainedGenerator()->getAddUrlPart();
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
            $m = Kwc_Abstract::createChildModel($this->_class);
            if (!$m) throw new Kwf_Exception("No child model set for '$this->_class'");
            $s = $m->select();
            $s->whereEquals('component_id', $idsToLoad);
            Kwf_Benchmark::count('DirectoryChainedGenerator getRows', count($idsToLoad));
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
        $ret = null;
        if ($data) {
            if (isset($data->chained)) {
                $ret = $data->chained;
            } else { // MasterAsChild (only called for inherit component which are on the page of the first found chained)
                while (!isset($data->chained)) $data = $data->parent;
                $ret = $data->chained->getPage();
            }
        }
        return $ret;
    }

    protected function _getChainedChildComponents($parentData, $select)
    {
        return $this->_getChainedGenerator()->getChildData(
            $this->_getChainedData($parentData), $this->_getChainedSelect($select)
        );
    }

    protected function _getChainedSelect($select)
    {
        $select = clone $select;
        if ($p = $select->getPart(Kwf_Component_Select::WHERE_CHILD_OF)) {
            $cd = $this->_getChainedData($p);
            if (!$cd) $cd = $p; // Falls Data ein parent von Komponente mit chainedType ist
            $select->whereChildOf($cd);
        }
        if ($cls = $select->getPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES)) {
            foreach ($cls as &$c) {
                $c = substr($c, strpos($c, '.')+1);
            }
            $select->whereComponentClasses($cls);
        }
        if ($sr = $select->getPart(Kwf_Component_Select::WHERE_SUBROOT)) {
            $newSr = array();
            foreach ($sr as $i) {
                if (isset($i->chained)) {
                    $newSr[] = $i->chained;
                } else {
                    $newSr[] = $i;
                }
            }
            $select->setPart(Kwf_Component_Select::WHERE_SUBROOT, $newSr);
        }
        return $select;
    }

    public function getChildData($parentDatas, $select = array())
    {
        Kwf_Benchmark::count('GenChained::getChildData');
        $ret = array();
        if (is_array($select)) $select = new Kwf_Component_Select($select);
        if ($id = $select->getPart(Kwf_Component_Select::WHERE_ID)) {
            if ($this->_getChainedGenerator() instanceof Kwc_Root_Category_Generator) {
                $select->whereId(substr($id, 1));
            }
        }

        $chainedType = $this->getGeneratorFlag('chainedType');

        $slaveData = $select->getPart(Kwf_Component_Select::WHERE_CHILD_OF);
        while ($slaveData) {
            if (Kwc_Abstract::getFlag($slaveData->componentClass, 'chainedType') == $chainedType) {
                break;
            }
            $slaveData = $slaveData->parent;
        }

        $parentDataSelect = new Kwf_Component_Select();
        $parentDataSelect->copyParts(array('ignoreVisible'), $select);

        $parentDatas = is_array($parentDatas) ? $parentDatas : array($parentDatas);
        foreach ($parentDatas as $parentData) {
            foreach ($this->_getChainedChildComponents($parentData, $select) as $component) {
                $pData = array();
                if (!$parentData) {
                    if (!$slaveData) {
                        $pData = Kwc_Chained_Abstract_Component::getAllChainedByMaster($component->parent, $chainedType, $parentDataSelect);
                    } else {
                        $chainedComponent = Kwc_Chained_Abstract_Component::getChainedByMaster($component->parent, $slaveData, $chainedType, $parentDataSelect);
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


    protected function _getComponentIdFromRow($parentData, $row)
    {
        return $parentData->componentId.$this->getIdSeparator().$this->_getIdFromRow($row);
    }

    protected function _formatConfig($parentData, $row)
    {
        $componentClass = $this->_settings['masterComponentsMap'][$row->componentClass];

        $id = $this->_getIdFromRow($row);
        $data = array(
            'componentId' => $this->_getComponentIdFromRow($parentData, $row),
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
        if (isset($row->isHome)) {
            $data['isHome'] = $row->isHome;
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
        $class = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $generatorKey = $this->_settings['generator'];
        return Kwf_Component_Generator_Abstract::getInstance($class, $generatorKey);;
    }

    public function getIdSeparator()
    {
        $ret = $this->_getChainedGenerator()->getIdSeparator();
        if (!$ret) $ret = '_'; //pages generator
        return $ret;
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
        return $ret;
    }


    public function getModel()
    {
        return $this->_getChainedGenerator()->getModel();
    }

    // TODO Cache
    public function getStaticCacheVarsForMenu()
    {
        $ret = $this->_getChainedGenerator()->getStaticCacheVarsForMenu();
        return $ret;
    }

    public function makeChildrenVisible($source)
    {
        if ($source->generator !== $this) {
            throw new Kwf_Exception("you must call this only with the correct source");
        }

        $m = Kwc_Abstract::createChildModel($this->_class);
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
        Kwc_Admin::getInstance($source->componentClass)->makeVisible($source);
    }

    public function duplicateChild($source, $parentTarget)
    {
        if ($source->generator !== $this) {
            throw new Kwf_Exception("you must call this only with the correct source");
        }

        //Annahme: sourceChildren und targetChildren müssen in der gleichen Reinhenfolge daherkommen
        //gibt es einen generator ohne pos oder datum oder ähnlichem?
        $sourceChildren = array_values($source->generator->getChildData($source->parent, array('ignoreVisible'=>true)));
        $targetChildren = array_values($this->getChildData($parentTarget, array('ignoreVisible'=>true)));

        $target = null;
        foreach ($sourceChildren as $i=>$sc) {
            if ($sc->componentId == $source->componentId) {
                $target = $targetChildren[$i];
            }
        }
        if ($m = Kwc_Abstract::createChildModel($this->_class)) {
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
        Kwc_Admin::getInstance($source->componentClass)->duplicate($source, $target);
        return $target;
    }

    public function getDeviceVisible(Kwf_Component_Data $data)
    {
        return $data->chained->getDeviceVisible();
    }
}
