<?php
class RedMallee_Breadcrumbs_Component extends Kwc_Menu_BreadCrumbs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['separator'] = '';
        return $ret;
    }
}
