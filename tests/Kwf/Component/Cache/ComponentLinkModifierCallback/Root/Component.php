<?php
class Kwf_Component_Cache_ComponentLinkModifierCallback_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators'] = array();
        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_ComponentLinkModifierCallback_Page1_Component'
        );
        $ret['generators']['linkTarget'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_ComponentLinkModifierCallback_LinkTargetPage_Component'
        );
        return $ret;
    }
}
