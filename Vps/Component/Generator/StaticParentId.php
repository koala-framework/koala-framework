<?php
class Vps_Component_Generator_StaticParentId extends Vps_Component_Generator_Static
{
    protected function _formatConfig($parentData, $componentKey)
    {
        $ret = parent::_formatConfig($parentData, $componentKey);
        $ret['dbId'] = substr($ret['dbId'], 0, strrpos($ret['dbId'], $this->_idSeparator));
        return $ret;
    }
}
