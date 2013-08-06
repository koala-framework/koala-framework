<?php

class Kwf_Component_Generator_Static extends Kwf_Component_Generator_Abstract
{
    protected $_idSeparator = '-';
    protected $_eventsClass = 'Kwf_Component_Generator_Events_Static';
    private $_hasHome = false;
    protected $_addUrlPart = true;

    protected function _init()
    {
        parent::_init();
        foreach ($this->_settings['component'] as $class) {
            if (Kwc_Abstract::hasSetting($class, 'dataClass') &&
                Kwc_Abstract::getSetting($class, 'dataClass') == 'Kwf_Component_Data_Home'
            ) {
                $this->_hasHome = true;
            }
        }
    }

    protected function _idMatches($id)
    {
        return in_array(substr($id, 1), array_keys($this->_settings['component']));
    }

    public function getChildData($parentData, $select = array())
    {
        Kwf_Benchmark::count('GenStatic::getChildData');

        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }

        $pData = $parentData;

        $ret = array();

        if ($p = $select->getPart(Kwf_Component_Select::WHERE_ID)) {
            if (!$this->_idMatches($p)) {
                return $ret;
            }
        }

        if (!$parentData) {
            if ($p = $select->getPart(Kwf_Component_Select::WHERE_CHILD_OF)) {
                throw new Kwf_Exception("this must not happen");
                $p = $p->getPageOrRoot();
                $parentData = $p->getRecursiveChildComponents(array(
                    'componentClass' => $this->_class
                ));
            } else {
                $parentSelect = new Kwf_Component_Select();
                $parentSelect->copyParts(array(
                    Kwf_Component_Select::WHERE_SUBROOT,
                    Kwf_Component_Select::IGNORE_VISIBLE,
                    Kwf_Component_Select::WHERE_CHILD_OF),
                    $select
                );
                $parentData = Kwf_Component_Data_Root::getInstance()
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
        throw new Kwf_Exception('getChildIds only supports table generators');
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
            if ($select->hasPart(Kwf_Model_Select::LIMIT_COUNT)) {
                if (count($ret) >= $select->getPart(Kwf_Model_Select::LIMIT_COUNT)) break;
            }
        }
        return $ret;
    }

    protected function _acceptKey($key, $select, $parentData)
    {
        $components = $this->_settings['component'];
        if (isset($components[$key]) && !$components[$key]) {
            return false;
        }
        if ($select->hasPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES)) {
            $value = $select->getPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES);
            $c = $components[$key];
            if (Kwc_Abstract::getFlag($c, 'hasAlternativeComponent')) {
                $cls = strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c;
                if (is_array($parentData)) $parentData = array_shift($parentData);
                $alt = call_user_func(array($cls, 'useAlternativeComponent'), $c, $parentData, $this);
                if ($alt) {
                    $altCmps = call_user_func(array($cls, 'getAlternativeComponents'), $c);
                    $c = $altCmps[$alt];
                }
            }
            if (!in_array($c, $value)) {
                return false;
            }
        }
        if ($select->getPart(Kwf_Component_Select::WHERE_HAS_EDIT_COMPONENTS)) {
            $editComponents = Kwc_Abstract::getSetting($this->_class, 'editComponents');
            if (!in_array($key, $editComponents)) {
                return false;
            }
        }
        if ($select->hasPart(Kwf_Component_Select::WHERE_ID)) {
            $value = $select->getPart(Kwf_Component_Select::WHERE_ID);
            if ($this->_idSeparator.$key != $value) {
                return false;
            }
        }
        return true;
    }

    protected function _getComponentIdFromRow($parentData, $componentKey)
    {
        $componentId = '';
        if ($parentData->componentId) {
            $componentId = $parentData->componentId . $this->_idSeparator;
        }
        $componentId .= $componentKey;
        return $componentId;
    }

    protected function _formatConfig($parentData, $componentKey)
    {
        $componentId = $this->_getComponentIdFromRow($parentData, $componentKey);
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

    public function duplicateChild($source, $parentTarget, Zend_ProgressBar $progressBar = null)
    {
        if (!$source || !$parentTarget) {
            throw new Kwf_Exception("source and parentTarget are required");
        }

        $progressBar = null; //stop here, as Generator_Table::getDuplicateProgressSteps doesn't go any deeper

        if ($source->generator !== $this) {
            throw new Kwf_Exception("you must call this only with the correct source");
        }
        $id = $this->_idSeparator . array_pop(explode($this->_idSeparator, $source->componentId));
        $target = $parentTarget->getChildComponent($id);
        if (!$target) {
            throw new Kwf_Exception("Didn't get child component '$id' from '$parentTarget->componentId' in generator '{$this->getGeneratorKey()}' of '$this->_class'");
        }
        Kwc_Admin::getInstance($source->componentClass)->duplicate($source, $target, $progressBar);
        return $target;
    }

    public function makeChildrenVisible($source)
    {
        if ($source->generator !== $this) {
            throw new Kwf_Exception("you must call this only with the correct source");
        }
        Kwc_Admin::getInstance($source->componentClass)->makeVisible($source);
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['static'] = true;
        if ($this->_hasHome) {
            $ret['hasHome'] = true;
        }
        return $ret;
    }

    protected function _formatSelectHome(Kwf_Component_Select $select)
    {
        if ($this->_hasHome) {
            return $select;
        } else {
            return parent::_formatSelectHome($select);
        }
    }

    public function getStaticChildComponentIds()
    {
        $childComponentIds = array();
        foreach (array_keys($this->getChildComponentClasses()) as $c) {
            $childComponentIds[] = $this->getIdSeparator().$c;
        }
        return $childComponentIds;
    }
}
