<?php
class Vpc_TreeCache_StaticBox extends Vpc_TreeCache_Static
{
    protected function _acceptKey($key, $constraints, $parentData)
    {
        $ret = parent::_acceptKey($key, $constraints, $parentData);
        if ($ret && isset($constraints['inherit'])) {
            $class = $this->_classes[$key];
            return !isset($class['inherit']) || ($class['inherit'] == $constraints['inherit']);
        }
        return $ret;
    }
    
    protected function _formatConfig($parentData, $key)
    {
        $ret = parent::_formatConfig($parentData, $key);
        $c = $this->_classes[$key];
        $ret['priority'] = isset($c['priority']) ? $c['priority'] : 0;
        $ret['inherit'] = !isset($c['inherit']) || $c['inherit'];
        $ret['box'] = isset($c['box']) ? $c['box'] : $key;
        return $ret;
    }
    public function createsBoxes()
    {
        return true;
    }
}
