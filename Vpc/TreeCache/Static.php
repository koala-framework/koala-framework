<?php
class Vpc_TreeCache_Static extends Vpc_TreeCache_Abstract
{
    protected $_classes;
    protected $_idSeparator = '-';

    public function getChildIds($parentData, $constraints = array())
    {
        $ret = parent::getChildIds($parentData, $constraints);
        $constraints = $this->_formatConstraints($parentData, $constraints);
        if (is_null($constraints)) return $ret;
        foreach (array_keys($this->_classes) as $key) {
            if ($this->_acceptKey($key, $constraints)) {
                $ret[] = $this->_idSeparator . $key;
            }
        }
        return $ret;
    }

    public function getChildData($parentData, $id)
    {
        $prefix = substr($id, 0, 1);
        $key = substr($id, 1);
        if ($prefix == $this->_idSeparator && isset($this->_classes[$key])) {
            return $this->_createData($this->_formatConfig($parentData, $key));
        }
        return parent::getChildData($parentData, $id);
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

    protected function _acceptKey($key, $constraints)
    {
        $ret = true;
        if ($ret && isset($constraints['componentClass'])) {
            if (!in_array($this->_getComponentClass($key), $constraints['componentClass'])) {
                $ret = false;
            }
        }
        return $ret;
    }

    protected function _formatConfig($parentData, $componentKey)
    {
        $c = $this->_classes[$componentKey];
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
            'componentClass' => $this->_getComponentClass($componentKey),
            'parent' => $parentData,
            'id' => $componentKey,
            'isPage' => false
        );
    }
    
    private function _getComponentClass($componentKey)
    {
        $c = $this->_classes[$componentKey];
        if (is_string($c)) {
            return $c;
        } else if (!isset($c['childComponentClass']) && isset($c['childClassKey'])) {
            return $this->_getChildComponentClass($c['childClassKey']);
        } else if (isset($c['componentClass'])) {
            return $c['componentClass'];
        } else {
            throw new Vps_Exception('ComponentClass is not set in ' . get_class($this));
        }
    }
}
