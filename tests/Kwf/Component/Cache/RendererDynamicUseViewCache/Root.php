<?php
class Kwf_Component_Cache_RendererDynamicUseViewCache_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_Component',
        );
        return $ret;
    }
}