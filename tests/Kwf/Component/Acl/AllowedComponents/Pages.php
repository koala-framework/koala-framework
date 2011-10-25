<?php
class Kwf_Component_Acl_AllowedComponents_Pages extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Acl_AllowedComponents_PageDetail'
        );
        return $ret;
    }
}