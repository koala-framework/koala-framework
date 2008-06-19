<?php
class Vpc_TreeCache_StaticBox extends Vpc_TreeCache_Static
{
    protected function _formatConfig($parentData, $componentKey)
    {
        $ret = parent::_formatConfig($parentData, $componentKey);
        $c = $this->_classes[$componentKey];
        $ret['priority'] = isset($c['priority']) ? $c['priority'] : 0;
        $ret['inherit'] = !isset($c['inherit']) || $c['inherit'];
        return $ret;
    }
}
