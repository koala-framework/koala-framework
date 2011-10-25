<?php
class Kwc_Trl_InheritContentWithVisible_Box_Child_Trl_ExtConfig extends Kwf_Component_Abstract_ExtConfig_Abstract
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