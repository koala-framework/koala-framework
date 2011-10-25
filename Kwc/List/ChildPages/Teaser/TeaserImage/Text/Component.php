<?php
    class Kwc_List_ChildPages_Teaser_TeaserImage_Text_Component
        extends Kwc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_List_ChildPages_Teaser_TeaserImage_Text_Model';
        return $ret;
    }
}
