<?php
class Vpc_Menu_TreeCache extends Vpc_TreeCache_Static
{
    protected $_classes = array(
        'subMenu' => array(
            'childClassKey' => 'subMenu'
        )
    );

    protected function _formatConstraints($parentData, $constraints)
    {
        $maxLevel = $this->_getSetting('maxLevel');
        if ($this->_getParentLevel($parentData) >= $maxLevel) {
            return null;
        }
        return parent::_formatConstraints($parentData, $constraints);
    }

    private function _getParentLevel($parentData)
    {
        if (isset($parentData->level)) {
            return $parentData->level;
        } else {
            $level = $this->_getSetting('level');
            if (is_string($level)) {
                return 1;
            }
            return $level;
        }
    }
    protected function _formatConfig($parentData, $componentKey)
    {
        $ret = parent::_formatConfig($parentData, $componentKey);
        $ret['level'] = $this->_getParentLevel($parentData) + 1;
        return $ret;
    }
}
