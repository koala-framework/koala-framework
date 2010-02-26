<?php
class Vpc_Trl_Text_German extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['text'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_Text_Text_Component',
            'name' => 'text',
        );
        return $ret;
    }
}
