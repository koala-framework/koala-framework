<?php
class GreyBox_Blog_Category_Directory_Component extends Kwc_Blog_Category_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] = 'GreyBox_Blog_Category_Detail_Component';
        return $ret;
    }
}
