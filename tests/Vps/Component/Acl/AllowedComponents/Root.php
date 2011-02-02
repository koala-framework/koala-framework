<?php
class Vps_Component_Acl_AllowedComponents_Root extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Vpc_Root_Category_Generator',
            'showInMenu' => true,
            'inherit' => true,
            'component' => array(
                'specialContainer' => 'Vps_Component_Acl_AllowedComponents_SpecialContainer',
                'pagesContainer' => 'Vps_Component_Acl_AllowedComponents_PagesContainer',
            ),
            'model' => 'Vps_Component_Acl_AllowedComponents_PagesModel'
        );
        return $ret;
    }
}
