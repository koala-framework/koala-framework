<?php
class Kwc_Trl_Text_German extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['text'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_Text_Text_Component',
            'name' => 'text',
        );
        return $ret;
    }
}
