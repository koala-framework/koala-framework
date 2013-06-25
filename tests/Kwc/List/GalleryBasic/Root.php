<?php
class Kwc_List_GalleryBasic_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_List_GalleryBasic_TestComponent',
        );
        return $ret;
    }
}
