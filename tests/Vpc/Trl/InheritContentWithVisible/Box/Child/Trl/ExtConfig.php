<?php
class Vpc_Trl_InheritContentWithVisible_Box_Child_Trl_ExtConfig extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $ret = array(
            'form' => array(
                'xtype' => 'panel'
            )
        );
        return $ret;
    }
}