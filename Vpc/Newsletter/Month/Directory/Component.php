<?php
class Vpc_Newsletter_Month_Directory_Component extends Vpc_Directories_Month_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['detail']['model'] = 'Vpc_Newsletter_Model';

        //für News-Kategorien Box
        $ret['categoryChildId'] = 'month';
        $ret['categoryName'] = trlVps('Months');

        $ret['dateColumn'] = 'create_date';

        return $ret;
    }
    public function getSelect()
    {
        $select = parent::getSelect();
        return $select;
    }

    public static function getViewCacheLifetimeForView()
    {
        return mktime(0, 0, 0, date('m'), date('d')+1, date('Y')) - time();
    }
}
