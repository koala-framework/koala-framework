<?php
class Vps_Component_ContentWidth_Page_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_ContentWidth_Page_Child_Component'
        );
        $ret['contentWidthSubtract'] = 10;
        return $ret;
    }
}
