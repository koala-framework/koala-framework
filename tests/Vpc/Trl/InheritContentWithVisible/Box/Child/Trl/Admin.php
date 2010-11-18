<?php
class Vpc_Trl_InheritContentWithVisible_Box_Child_Trl_Admin extends Vpc_Admin
{

    public function getExtConfig($type = self::EXT_CONFIG_DEFAULT)
    {
        $ret = array(
            'form' => array(
                'xtype' => 'panel'
            )
        );
        return $ret;
    }
  
}
