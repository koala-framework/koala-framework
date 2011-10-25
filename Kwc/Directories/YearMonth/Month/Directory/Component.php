<?php
class Kwc_Directories_YearMonth_Month_Directory_Component extends Kwc_Directories_Month_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] = 'Kwc_Directories_YearMonth_Month_Detail_Component';
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_FirstChildPage_Data';
        return $ret;
    }
}
