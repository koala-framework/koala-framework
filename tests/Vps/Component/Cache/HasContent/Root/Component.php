<?php
class Vps_Component_Cache_HasContent_Root_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Cache_HasContent_Root_Child_Component',
            'name' => 'child'
        );
        return $ret;
    }
}
