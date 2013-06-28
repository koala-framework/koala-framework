<?php
class Kwc_Basic_Image_Cache_Root_ImagesEnlargeComponent extends Kwc_List_Gallery_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Kwc_Basic_Image_Cache_Root_ImageEnlargeComponent';
        $ret['childModel'] = 'Kwc_Basic_Image_Cache_Root_ListModel';
        $ret['ownModel'] = 'Kwc_Basic_Image_Cache_Root_ListOwnModel';
        return $ret;
    }

    protected function _getMasterChildContentWidth($data)
    {
        return 200;
    }
}
