<?php
class Kwf_Component_Generator_StaticPageUnderStatic_C2_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['component3'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_StaticPageUnderStatic_C1_Component'
        );
        return $ret;
    }
}
