<?php
class Kwc_Trl_Headline_German extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['headline'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_Headline_Headline_Component',
            'name' => 'headline',
        );
        return $ret;
    }
}
