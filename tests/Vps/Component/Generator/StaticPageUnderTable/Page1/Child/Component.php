<?php
class Vps_Component_Generator_StaticPageUnderTable_Page1_Child_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'page',
            'component' => 'Vpc_Basic_Empty_Component'
        );
        return $ret;
    }
}
