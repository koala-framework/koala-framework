<?php
class Vps_Component_ContentWidthBox_PageWithEmptyBox_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['testBox'] = array(
            'component' => 'Vpc_Basic_Empty_Component',
            'class' => 'Vps_Component_Generator_Box_Static',
            'unique' => true,
            'inherit' => true
        );
        return $ret;
    }
}
