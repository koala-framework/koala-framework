<?php

class Vps_Component_Generator_Static extends Vps_Component_Generator_Abstract
{
    protected $_idSeparator = '-';

    public function getChildIds($parentData, $constraints = array())
    {
        $ret = parent::getChildIds($parentData, $constraints);
        if (!$parentData) {
            throw new Vps_Exception("no parentData for getChildIds is not (yet) implemented");
        }
        foreach ($this->_fetchKeys($parentData, $constraints) as $key) {
            $ret[] = $this->_idSeparator . $key;
        }
        return $ret;
    }
    public function getChildData($parentData, $constraints = array())
    {
        if (isset($this->_settings['unique']) && $this->_settings['unique']) {
            $component = $parentData;
            while ($component && $component->componentClass != $this->_class) {
                if ($component->componentClass != $this->_class) {
                    foreach ($component->getRecursiveChildComponents(array('page' => false, 'unique' => true)) as $c) {
                        if ($c->getParent()->componentClass == $this->_class) {
                            $component = $c->getParent();
                        }
                    }
                }
                if ($component->componentClass != $this->_class) {
                    if ($component->parent instanceof Vps_Component_Data_Root) {
                        $component = $component->getParent();
                    } else {
                        $component = $component->getParentPage();
                    }
                }
            }
            if ($component) {
                $parentData = $component;
            } else {
                $component = $this->_settings['generator'];
                throw new Vps_Exception("Couldn't find unique component '$component'");
            }
        }
        $ret = parent::getChildData($parentData, $constraints);
        if (!$parentData) {
            if (isset($constraints['id'])) {
                //Performance: wenn id contraint schauen ob es überhaupt was gibt
                //wenn nicht parentDatas nicht ermitteln (kann aufwändig sein)
                $id = substr($constraints['id'], 1);
                if (substr($constraints['id'], 0, 1) == $this->_idSeparator || !isset($this->_settings['component'][$id])) {
                    return $ret;
                }
            }
            $parentDatas = Vps_Component_Data_Root::getInstance()
                                        ->getComponentsByClass($this->_class);
        } else {
            $parentDatas = array($parentData);
        }
        foreach ($parentDatas as $parentData) {
            foreach ($this->_fetchKeys($parentData, $constraints) as $key) {
                $ret[] = $this->_createData($parentData, $key, $constraints);
            }
        }
        return $ret;
    }

    protected function _fetchKeys($parentData, $constraints)
    {
        $ret = array();
        $constraints = $this->_formatConstraints($parentData, $constraints);
        if (is_null($constraints)) return array();
        foreach (array_keys($this->_settings['component']) as $key) {
            if ($this->_acceptKey($key, $constraints, $parentData)) {
                $ret[] = $key;
            }
        }
        return $ret;
    }

    protected function _formatConstraints($parentData, $constraints)
    {
        $constraints = parent::_formatConstraints($parentData, $constraints);
        if (is_null($constraints)) return null;
        if (isset($constraints['filename'])) {
            return null;
        }
        if (isset($constraints['showInMenu'])) {
            return null;
        }
        if (isset($constraints['componentClass'])) {
            if (!is_array($constraints['componentClass'])) {
                $constraints['componentClass'] = array($constraints['componentClass']);
            }
        }
        return $constraints;
    }

    protected function _acceptKey($key, $constraints, $parentData)
    {
        $ret = true;
        if (isset($this->_settings['component'][$key]) && !$this->_settings['component'][$key]) {
            $ret = false;
        }
        if ($ret && isset($constraints['componentClass'])) {
            if (!in_array($this->_settings['component'][$key], $constraints['componentClass'])) {
                $ret = false;
            }
        }
        if ($ret && isset($constraints['id'])) {
            if ($this->_idSeparator.$key != $constraints['id']) {
                $ret = false;
            }
        }
        if ($ret && isset($constraints['inherit'])) {
            $ret = !isset($this->_settings['inherit']) || ($this->_settings['inherit'] == $constraints['inherit']);
        }
        return $ret;
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
            'componentClass' => $this->_settings['component'][$componentKey],
            'parent' => $parentData,
            'isPage' => false,
            'isPseudoPage' => false,
            'visible' => true,
            'priority' => $priority,
            'inherit' => $inherit
        );
    }
    protected function _getIdFromRow($componentKey)
    {
        return $componentKey;
    }
}
