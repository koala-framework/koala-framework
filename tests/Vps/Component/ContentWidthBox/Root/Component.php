<?php
class Vps_Component_ContentWidthBox_Root_Component extends Vpc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        $ret['contentWidth'] = 800;
        $ret['contentWidthBoxSubtract'] = array(
            'testBox' => 100
        );
        $ret['generators']['page'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'page',
            'component' => 'Vps_Component_ContentWidthBox_Page_Component'
        );
        $ret['generators']['pageWithEmptyBox'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'pageWithEmptyBox',
            'component' => 'Vps_Component_ContentWidthBox_PageWithEmptyBox_Component'
        );
        $ret['generators']['testBox'] = array(
            'component' => 'Vps_Component_ContentWidthBox_Box_Component',
            'class' => 'Vps_Component_Generator_Box_Static',
            'unique' => true,
            'inherit' => true
        );
        return $ret;
    }
}
