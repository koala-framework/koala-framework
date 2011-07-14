<?php
class Vps_Component_ContentWidth_Root_Component extends Vpc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);
        $ret['contentWidth'] = 800;
        $ret['contentWidthBoxSubtract'] = array();
        $ret['generators']['page'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'page',
            'component' => 'Vps_Component_ContentWidth_Page_Component'
        );
        return $ret;
    }
}
