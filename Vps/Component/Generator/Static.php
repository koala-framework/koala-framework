<?php

class Vps_Component_Generator_Static extends Vps_Component_Generator_Abstract
{
    protected $_idSeparator = '-';

    public function getChildData($parentData, $select = array())
    {
        Vps_Benchmark::count('GenStatic::getChildData');

        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }

        $pData = $parentData;

        $ret = array();
        if (!$parentData) {
            if ($p = $select->getPart(Vps_Component_Select::WHERE_CHILD_OF_SAME_PAGE)) {
                throw new Vps_Exception("this must not happen");
                $p = $p->getPageOrRoot();
                $parentData = $p->getRecursiveChildComponents(array(
                    'componentClass' => $this->_class
                ));
            } else {
                $parentSelect = new Vps_Component_Select();
                $parentSelect->copyParts(array(
                    Vps_Component_Select::WHERE_SUBROOT,
                    Vps_Component_Select::WHERE_CHILD_OF_SAME_PAGE),
                    $select
                );
                $parentData = Vps_Component_Data_Root::getInstance()
                            ->getComponentsBySameClass($this->_class, $parentSelect);
            }
        }
        $parentDatas = is_array($parentData) ? $parentData : array($parentData);
        foreach ($this->_fetchKeys($pData, $select) as $key) {
            foreach ($parentDatas as $parentData) {
                $data = $this->_createData($parentData, $key, $select);
                if ($data) $ret[] = $data;
            }
        }
        return $ret;
    }

    public function getChildIds($parentData, $select = array())
    {
        throw new Vps_Exception('getChildIds only supports table generators');
    }

    protected function _fetchKeys($parentData, $select)
    {
        $ret = array();
        $select = $this->_formatSelect($parentData, $select);
        if (is_null($select)) return array();

        foreach (array_keys($this->_settings['component']) as $key) {
            if ($this->_acceptKey($key, $select, $parentData)) {
                $ret[] = $key;
            }
            if ($select->hasPart(Vps_Model_Select::LIMIT_COUNT)) {
                if (count($ret) >= $select->getPart(Vps_Model_Select::LIMIT_COUNT)) break;
            }
        }
        return $ret;
    }

    protected function _acceptKey($key, $select, $parentData)
    {
        if (isset($this->_settings['component'][$key]) && !$this->_settings['component'][$key]) {
            return false;
        }
        if ($select->hasPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES)) {
            $value = $select->getPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES);
            if (!in_array($this->_settings['component'][$key], $value)) {
                return false;
            }
        }
        if ($select->getPart(Vps_Component_Select::WHERE_HAS_EDIT_COMPONENTS)) {
            $editComponents = Vpc_Abstract::getSetting($this->_class, 'editComponents');
            if (!in_array($key, $editComponents)) {
                return false;
            }
        }
        if ($select->hasPart(Vps_Component_Select::WHERE_ID)) {
            $value = $select->getPart(Vps_Component_Select::WHERE_ID);
            if ($this->_idSeparator.$key != $value) {
                return false;
            }
        }
        return true;
    }

    protected function _formatConfig($parentData, $componentKey)
    {
        $componentId = '';
        if ($parentData->componentId) {
            $componentId = $parentData->componentId . $this->_idSeparator;
        }
        $componentId .= $componentKey;
        $dbId = '';
        if ($parentData->dbId) {
            $dbId = $parentData->dbId . $this->_idSeparator;
        }
        $dbId .= $componentKey;

        $c = $this->_settings;
        $priority = isset($c['priority']) ? $c['priority'] : 0;
        $inherit = !isset($c['inherit']) || $c['inherit'];

        return array(
            'componentId' => $componentId,
            'dbId' => $dbId,
            'componentClass' => $this->_getChildComponentClass($componentKey, $parentData),
            'parent' => $parentData,
            'isPage' => false,
            'isPseudoPage' => false,
            'priority' => $priority,
            'inherit' => $inherit
        );
    }
    protected function _getIdFromRow($componentKey)
    {
        return $componentKey;
    }

    public function duplicateChild($source, $parentTarget)
    {
        if ($source->generator !== $this) {
            throw new Vps_Exception("you must call this only with the correct source");
        }
        $id = $this->_idSeparator . array_pop(explode($this->_idSeparator, $source->componentId));
        $target = $parentTarget->getChildComponent($id);
        Vpc_Admin::getInstance($source->componentClass)->duplicate($source, $target);
        return $target;
    }

    public function makeChildrenVisible($source)
    {
        if ($source->generator !== $this) {
            throw new Vps_Exception("you must call this only with the correct source");
        }
        Vpc_Admin::getInstance($source->componentClass)->makeVisible($source);
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['static'] = true;
        return $ret;
    }
}
