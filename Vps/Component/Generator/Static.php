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
                $ret[] = $this->_createData($parentData, $key);
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
        if (isset($constraints['page']) && $constraints['page']) {
            return null;
        }
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

        return array(
            'componentId' => $componentId,
            'dbId' => $dbId,
            'componentClass' => $this->_settings['component'][$componentKey],
            'parent' => $parentData,
            'isPage' => false
        );
    }
    protected function _getIdFromRow($componentKey)
    {
        return $componentKey;
    }
}
