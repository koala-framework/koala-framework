<?php
class Vpc_TreeCache_Static extends Vpc_TreeCache_Abstract
{
    protected $_classes;
    protected $_idSeparator = '-'; //um in StaticPage _ verwenden zu können

    public function getChildData($parentData, $constraints = array())
    {
        $ret = parent::getChildData($parentData, $constraints);
        $constraints = $this->_formatConstraints($parentData, $constraints);
        if (!is_null($constraints)) {
            if (isset($constraints['id'])) {
                if (isset($this->_classes[$constraints['id']])) {
                    $ret[] = new $this->_pageDataClass($this->_formatConfig($parentData, $constraints['id']));
                }
            } else {
                if (isset($constraints['componentClass'])) {
                    if (!is_array($constraints['componentClass'])) {
                        $contraintClasses = array($constraints['componentClass']);
                    } else {
                        $contraintClasses = $constraints['componentClass'];
                    }
                }
                foreach (array_keys($this->_classes) as $key) {
                    if (isset($contraintClasses) &&
                            !in_array($this->_getComponentClass($key), $contraintClasses))
                    {
                        continue;
                    }
                    $ret[] = new $this->_pageDataClass($this->_formatConfig($parentData, $key));
                }
            }
        }
        return $ret;
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
            'id' => $componentKey
        );
    }
}
