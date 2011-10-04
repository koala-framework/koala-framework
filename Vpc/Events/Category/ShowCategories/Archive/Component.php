<?php
class Vpc_Events_Category_ShowCategories_Archive_Component extends Vpc_Directories_Category_ShowCategories_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['useDirectorySelect'] = false;
        return $ret;
    }

    protected function _getItemDirectory()
    {
        return $this->getData()->parent->getComponent()->getItemDirectory();
    }

    public function getCategoryIds()
    {
        return $this->getData()->parent->getComponent()->getCategoryIds();
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        if (!$ret) return null;
        $ret->where('IF(ISNULL(end_date), start_date, end_date) < NOW()');
        return $ret;
    }

    public static function getViewCacheLifetimeForView()
    {
        return mktime(0, 0, 0, date('m'), date('d')+1, date('Y')) - time();
    }
}
