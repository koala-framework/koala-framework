<?php
class Vpc_Directories_YearMonth_Month_Directory_Component extends Vpc_Directories_Month_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] = 'Vpc_Directories_YearMonth_Month_Detail_Component';
        $ret['dataClass'] = 'Vpc_Basic_LinkTag_FirstChildPage_Data';
        return $ret;
    }
}
