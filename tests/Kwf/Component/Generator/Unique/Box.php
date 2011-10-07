<?php
class Vps_Component_Generator_Unique_Box extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Generator_Unique_Page',
            'name' => 'page'
        );
        return $ret;
    }

}
