<?php
class Kwf_Component_Acl_AllowedComponents_Root extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Kwc_Root_Category_Generator',
            'showInMenu' => true,
            'inherit' => true,
            'component' => array(
                'specialContainer' => 'Kwf_Component_Acl_AllowedComponents_SpecialContainer',
                'pagesContainer' => 'Kwf_Component_Acl_AllowedComponents_PagesContainer',
            ),
            'model' => 'Kwf_Component_Acl_AllowedComponents_PagesModel'
        );
        return $ret;
    }
}
