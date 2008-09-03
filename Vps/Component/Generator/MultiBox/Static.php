<?php
class Vps_Component_Generator_MultiBox_Static extends Vps_Component_Generator_Static
    implements Vps_Component_Generator_MultiBox_Interface
{
    protected function _formatConfig($parentData, $key)
    {
        $ret = parent::_formatConfig($parentData, $key);
        $ret['multiBox'] = isset($this->_settings['box']) ? $this->_settings['box'] : $key;
        return $ret;
    }
}
