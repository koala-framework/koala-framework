<?php
class Kwf_Component_Generator_IsVisible_Child extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Basic_Empty_Component'
        );
        return $ret;
    }
}
?>