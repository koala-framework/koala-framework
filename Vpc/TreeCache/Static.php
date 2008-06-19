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
                foreach ($this->_classes as $key => $val) {
                    $ret[] = new $this->_pageDataClass($this->_formatConfig($parentData, $key));
                }
            }
        }
        return $ret;
    }
    
    protected function _formatConfig($parentData, $componentKey)
    {
        $c = $this->_classes[$componentKey];
        if (is_string($c)) {
            $componentClass = $c;
        } else if (!isset($c['childComponentClass']) && isset($c['childClassKey'])) {
            $componentClass = $this->_getChildComponentClass($c['childClassKey']);
        } else if (isset($c['componentClass'])) {
            $componentClass = $c['componentClass'];
        } else {
            throw new Vps_Exception('ComponentClass is not set in ' . get_class($this));
        }
        
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
            'componentClass' => $componentClass,
            'parent' => $parentData,
            'id' => $componentKey
        );
    }
}
