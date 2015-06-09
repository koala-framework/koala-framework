<?php
class Kwc_News_Month_Directory_Component extends Kwc_Directories_Month_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['detail']['model'] = 'Kwc_News_Directory_Model';

        //für News-Kategorien Box
        $ret['categoryName'] = trlKwfStatic('Months');

        $ret['dateColumn'] = 'publish_date';

        return $ret;
    }
    public function getSelect()
    {
        $select = parent::getSelect();
        $select->where('publish_date <= CURDATE()');
        if ($this->_getItemDirectorySetting('enableExpireDate')) {
            $select->where('expiry_date >= CURDATE() OR ISNULL(expiry_date)');
        }
        return $select;
    }

    public static function getViewCacheLifetimeForView()
    {
        return mktime(0, 0, 0, date('m'), date('d')+1, date('Y')) - time();
    }
}
