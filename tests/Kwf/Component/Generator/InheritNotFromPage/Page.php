<?php
class Kwf_Component_Generator_InheritNotFromPage_Page extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_InheritNotFromPage_PageChild'
        );
        return $ret;
    }
}
