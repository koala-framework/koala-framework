<?php
class Kwc_Articles_Detail_Component extends Kwc_Directories_Item_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['content'] = 'Kwc_Articles_Detail_Paragraphs_Component';
        $ret['generators']['child']['component']['previewImage'] = 'Kwc_Articles_Detail_PreviewImage_Component';
        $ret['generators']['child']['component']['favor'] = 'Kwc_Articles_Detail_Favor_Component';

        $ret['editComponents'] = array('content');
        return $ret;
    }
}
