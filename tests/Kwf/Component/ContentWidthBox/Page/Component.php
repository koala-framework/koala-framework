<?php
class Vps_Component_ContentWidthBox_Page_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['pageWithEmptyBox'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'pageWithEmptyBox',
            'component' => 'Vps_Component_ContentWidthBox_PageWithEmptyBox_Component'
        );
        return $ret;
    }
}
