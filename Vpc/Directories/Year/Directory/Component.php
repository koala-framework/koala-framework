<?php
class Vpc_Directories_Year_Directory_Component extends Vpc_Directories_Month_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['class'] = 'Vpc_Directories_Year_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Vpc_Directories_Year_Detail_Component';
        return $ret;
    }
}
