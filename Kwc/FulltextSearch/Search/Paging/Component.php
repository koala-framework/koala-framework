<?php
class Kwc_FulltextSearch_Search_Paging_Component extends Kwc_Paging_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }
}
