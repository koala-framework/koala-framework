<?php
class Vpc_Menu_Generator extends Vps_Component_Generator_Static
{
    protected function _formatSelect($parentData, $select)
    {
        $maxLevel = Vpc_Abstract::getSetting($this->_class, 'maxLevel');
        if ($this->_getParentLevel($parentData) >= $maxLevel) {
            return null;
        }
        return parent::_formatSelect($parentData, $select);
    }

    private function _getParentLevel($parentData)
    {
        if (isset($parentData->level)) {
            return $parentData->level;
        } else {
            $level = Vpc_Abstract::getSetting($this->_class, 'level');
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
