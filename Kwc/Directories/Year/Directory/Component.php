<?php
class Kwc_Directories_Year_Directory_Component extends Kwc_Directories_Month_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['detail']['class'] = 'Kwc_Directories_Year_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Kwc_Directories_Year_Detail_Component';
        return $ret;
    }
}
