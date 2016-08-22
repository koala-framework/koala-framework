<?php
class Kwc_Basic_Image_CacheParentImage_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        unset($ret['generators']['title']);
        unset($ret['generators']['page']);
        unset($ret['generators']['box']);

        $ret['generators']['image'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'image',
            'component' => 'Kwc_Basic_Image_CacheParentImage_Image_Component'
        );

        return $ret;
    }
}