<?php
class Vpc_Trl_Paragraphs_German extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_Paragraphs_Paragraphs_Component',
            'name' => 'test',
        );
        return $ret;
    }
}
