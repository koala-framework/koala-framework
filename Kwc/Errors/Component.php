<?php
class Kwc_Errors_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['accessDenied'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Errors_AccessDenied_Component'
        );
        $ret['generators']['notFound'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Errors_NotFound_Component'
        );
        return $ret;
    }
}
