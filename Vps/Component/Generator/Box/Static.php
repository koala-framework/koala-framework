<?php
class Vps_Component_Generator_Box_Static extends Vps_Component_Generator_Static implements Vps_Component_Generator_Box_Interface
{
    protected function _acceptKey($key, $constraints, $parentData)
    {
        $ret = parent::_acceptKey($key, $constraints, $parentData);
        if ($ret && isset($constraints['inherit'])) {
            $ret = !isset($this->_settings['inherit']) || ($this->_settings['inherit'] == $constraints['inherit']);
        }
        return $ret;
    }
    
    protected function _formatConfig($parentData, $key)
    {
        $ret = parent::_formatConfig($parentData, $key);
        $c = $this->_settings;
        $ret['priority'] = isset($c['priority']) ? $c['priority'] : 0;
        $ret['inherit'] = !isset($c['inherit']) || $c['inherit'];
        $ret['box'] = isset($c['box']) ? $c['box'] : $key;
        return $ret;
    }
}
