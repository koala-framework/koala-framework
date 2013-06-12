<?php
class Kwc_Blog_Category_Detail_Component extends Kwc_Directories_Category_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasComponentLinkModifiers'] = false;
        return $ret;
    }
}
