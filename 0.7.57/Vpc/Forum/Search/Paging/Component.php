<?php
class Vpc_Forum_Search_Paging_Component extends Vpc_Paging_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['includedParams'][] = 'search';
        return $ret;
    }
}
