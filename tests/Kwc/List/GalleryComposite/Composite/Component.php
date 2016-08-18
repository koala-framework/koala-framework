<?php
class Kwc_List_GalleryComposite_Composite_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['imageEnlarge'] = 'Kwc_List_GalleryComposite_ImageEnlarge_TestComponent';
        return $ret;
    }
}
