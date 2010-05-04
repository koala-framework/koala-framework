<?php
    class Vpc_List_ChildPages_Teaser_TeaserImage_Text_Component
        extends Vpc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_List_ChildPages_Teaser_TeaserImage_Text_Model';
        return $ret;
    }
}
