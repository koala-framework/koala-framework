<?php
class Kwf_Component_Generator_Inherit_Box extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['flag'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_Inherit_Flag'
        );
        return $ret;
    }

}
