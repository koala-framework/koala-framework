<?php
class Vpc_Root_LanguageRoot_Component extends Vpc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['language'] = array(
            'class' => 'Vps_Component_Generator_PseudoPage_Static',
            'component' => array(
                'de'=>'Vpc_Root_LanguageRoot_Language_Component',
                'en'=>'Vpc_Root_LanguageRoot_Language_Component'
            )
        );
        return $ret;
    }
}
