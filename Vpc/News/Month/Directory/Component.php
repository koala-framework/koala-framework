<?php
class Vpc_News_Month_Directory_Component extends Vpc_Directories_Month_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['detail']['model'] = 'Vpc_News_Directory_Model';

        //für News-Kategorien Box
        $ret['categoryName'] = trlVps('Months');

        $ret['dateColumn'] = 'publish_date';

        return $ret;
    }
    public function getSelect()
    {
        $select = parent::getSelect();
        $select->where('publish_date <= NOW()');
        if ($this->_getItemDirectorySetting('enableExpireDate')) {
            $select->where('expiry_date >= NOW() OR ISNULL(expiry_date)');
        }
        return $select;
    }

    public static function getViewCacheLifetimeForView()
    {
        return mktime(0, 0, 0, date('m'), date('d')+1, date('Y')) - time();
    }
}
