<?php
class Vpc_News_Year_Directory_Component extends Vpc_News_Month_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['categoryName'] = trlVps('Years');
        $ret['generators']['detail']['class'] = 'Vpc_Directories_Year_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Vpc_Directories_Year_Detail_Component';
        return $ret;
    }
}
